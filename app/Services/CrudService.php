<?php

namespace App\Services;

use App\Exceptions\RespostaException;
use App\Helpers\AuthorizesActions;
use App\Helpers\Resposta;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use stdClass;

abstract class CrudService
{
    use AuthorizesActions;

    public function __construct()
    {
    }

    protected function tratamentoExceptions(Exception $exception, ?object $validator = null)
    {
        $type = get_class($exception);
        switch ($type) {
            case ValidationException::class:
                $errors = $validator->errors();
                return Resposta::object(false, null, null, $errors->toArray());
                break;
            case ModelNotFoundException::class:
                return Resposta::object(false, null, null, [__('services.erros.no_results')]);
                break;
            case RespostaException::class:
                return Resposta::object($exception->status, $exception->getMessage(), $exception->dados, $exception->erros);
                break;
            default:
                $erros = (config('app.debug')) ? array(
                    __('services.erros.erro_interno'), get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine()
                ) : array(
                    __('services.erros.erro_interno')
                );
                return Resposta::object(false, null, null, $erros);
        }
    }

    /**
     * @param array $params
     * @return Resposta
     */
    public function list($params = [])
    {
        $model = $this->getModel();
        $authorize = $this->authorize('viewAny', $model);
        if (!$authorize->status) {
            return $authorize;
        }
        $page = $params['page'] ?? 1;
        $perPage = $params['perPage'] ?? $this->perPage($model);
        $list = $this->prepareList($this->applyFilters($model, $params), $params);
        if (!isset($params['paginate']) || $params['paginate'] == 'true') {
            $items = $list->paginate($perPage, ['*'], 'page', $page);
        } else {
            $items = $list->get();
        }
        foreach ($items as $model) {
            $this->prepareItem($model);
        }
        $items = $items instanceof Arrayable ? $items->toArray() : $items;
        return Resposta::object(true, null, $items, null);
    }

    /**
     * @param string|int $id
     * @param array $params
     * @return Resposta
     */
    public function search($id, $params = [])
    {
        try {

            $model = $this->applyFilters($this->getModel(), $params)->findOrFail($id);
            $authorize = $this->authorize('view', $model);
            if (!$authorize->status) {
                return $authorize;
            }
            return Resposta::object(true, null, [$this->prepareItem($model)], null);
        } catch (Exception $exception) {
            return $this->tratamentoExceptions($exception, new stdClass());
        }
    }

    /**
     * @param Model $model
     * @param array $params
     * @return Model
     */
    private function applyFilters($model, $params)
    {
        $filters = $this->prepareFilters($model, $params);
        $query = $model->where(function ($query) use ($filters) {
            foreach ($filters['and'] as $condition) {
                $searchTerm = $condition[2];
                if ($condition[1] == 'like') {
                    $searchTerm = "%{$searchTerm}%";
                }
                $query->where($condition[0], $condition[1], $searchTerm);
            }
        })->where(function ($query) use ($filters) {
            foreach ($filters['or'] as $condition) {
                $query->orWhere($condition[0], $condition[1], $condition[2]);
            }
        });
        if (isset($filters['in'][0])) {
            foreach ($filters['in'] as $key => $filterIn) {
                $query->whereIn($filters['in'][$key][0], $filters['in'][$key][1]);
            }
        }
        if (isset($params['order']) && is_string($params['order'])) {
            $order = explode('-', $params['order']);
            $query->orderBy($order[0], $order[1]);
        }
        return $query;
    }

    /**
     * @param array $data
     * @throws ValidationException
     * @return Resposta
     */
    public function save($data)
    {
        $authorize = $this->authorize('create', $this->getModel());
        if (!$authorize->status) {
            return $authorize;
        }
        $validator = $this->getValidator($data, true, null);
        try {
            $validator->validate();
            $data = !empty($this->getRules($data, false, $this->getModel())) ? $validator->validated() : $data;
            return Resposta::object(true, null, $this->performSave($data, $this->beforeSave($data)), null);
        } catch (Exception $exception) {
            return $this->tratamentoExceptions($exception, $validator);
        }
    }

    /**
     * @param array $data
     * @return array
     */
    protected function beforeSave($data)
    {
        return [];
    }

    /**
     * @param array $data
     * @param array $additionalData
     * @return array
     */
    protected function performSave($data, $additionalData)
    {
        return DB::transaction(function () use ($data, $additionalData) {
            $data = $this->prepareSave($data, $additionalData);
            $model = $this->getModel($this->prepareFromFillable($data, $additionalData));
            $model->save();
            return $this->postSave($model, $data, $additionalData);
        });
    }

    /**
     * @param string|int $id
     * @param array $data
     * @return Resposta
     */
    public function update($id, $data)
    {
        try {
            $model = $this->getModel();
            $model = $this->primaryKeyMultiple($data, $id, $model);
            $authorize = $this->authorize('update', $model);
            if (!$authorize->status)
                return $authorize;

            $validator = $this->getValidator($data, false, $model);
            try {
                $validator->validate();
                $data = !empty($this->getRules($data, true, $model)) ? $validator->validated() : $data;
                return Resposta::object(true, null, [$this->performUpdate($model, $data, $this->beforeUpdate($data, $model))], null);
            } catch (Exception $exception) {
                return $this->tratamentoExceptions($exception, $validator);
            }
        } catch (Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return Resposta::object(false, null, null, [__('services.erros.no_results')]);
            }
        }
    }

    /**
     * @param string|int $id
     * @param array $data
     * @param model $model
     * @return Model
     */
    protected function primaryKeyMultiple($data, $id, $model)
    {
        if (isset($data['primaryKey'])) {
            return $model->where($data['primaryKey'])->firstOrFail();
        } else {
            return $model->findOrFail($id);
        }
    }

    /**
     * @param array $data
     * @param Model $model
     * @return array
     */
    protected function beforeUpdate($data, $model)
    {
        return [];
    }

    /**
     * @param Model $model
     * @param array $data
     * @param array $additionalData
     * @return array
     */
    protected function performUpdate($model, $data, $additionalData)
    {
        return DB::transaction(function () use ($model, $data, $additionalData) {
            $model->update($this->guardFromUpdate($model, $this->prepareFromFillable($this->prepareUpdate($model, $data, $additionalData), $additionalData)));
            return $this->postUpdate($model, $data, $additionalData);
        });
    }

    /**
     * @param string|int $id
     * @return array
     */
    public function destroy($id)
    {
        $model = $this->getModel()->findOrFail($id);
        $authorize = $this->authorize('delete', $model);
        if (!$authorize->status) {
            return $authorize;
        }

        $model->delete();
        return [];
    }

    public function changeStatus($id)
    {
        $model = $this->getModel()->findOrFail($id);
        $authorize = $this->authorize('restore', $model);
        if (!$authorize->status) {
            return $authorize;
        }
        return $this->performStatusChange($model);
    }

    protected function performStatusChange($model)
    {
        $model->update(['ativo' => !$model->ativo]);
        return [];
    }

    /**
     * @param array $data
     * @param bool $saving
     * @param Model $model
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidator($data, $saving, $model)
    {
        return Validator::make($data, $this->getRules($data, $saving, $model), $this->getCustomMessages(), $this->getCustomAttributes());
    }

    protected function getRules($data, $saving, $model)
    {
        return [];
    }

    protected function getCustomAttributes()
    {
        return [];
    }

    protected function getCustomMessages()
    {
        return [];
    }

    /**
     * @return Model
     */
    protected function getModel($data = [])
    {
        $model = 'App\\Models\\' . str_replace('Service', '', class_basename($this));
        return new $model($data);
    }

    /**
     * @param array $data
     * @param array $additionalData
     * @return array
     */
    protected function prepareSave(array $data, array $additionalData): array
    {
        $data['empresaId'] = Auth::user()->empresaId ?? null;
        return array_merge($data, $additionalData);
    }

    /**
     * @param Model $model
     * @param array $data
     * @param array $additionalData
     * @return array
     */
    protected function postSave(Model $model, array $data, array $additionalData): array
    {
        $pk = $model->getKeyName();
        return [
            'id' => $model->$pk
        ];
    }

    /**
     * @param Model $model
     * @param array $data
     * @param array $additionalData
     * @return array
     */
    protected function prepareUpdate(Model $model, array $data, array $additionalData): array
    {
        return array_merge($data, $additionalData);
    }

    protected function guardFromUpdate($model, $finalData)
    {
        $guard = $model->guardFromUpdate ?? [];
        foreach ($guard as $key) {
            unset($finalData[$key]);
        }
        return $finalData;
    }

    /**
     * @param Model $model
     * @param array $data
     * @param array $additionalData
     * @return array
     */
    protected function postUpdate(Model $model, array $data, array $additionalData): array
    {
        return [];
    }

    /**
     * @param Model $model
     * @param array $params
     * @return array
     */
    protected function prepareFilters($model, $params)
    {
        $queryFilters = $model->queryFilters ?? [];
        $finalParams = [
            'or' => [],
            'and' => []
        ];
        if (!blank($queryFilters) && !blank($params)) {
            foreach ($params as $key => $value) {
                if (isset($queryFilters['or']) && array_key_exists($key, $queryFilters['or'])) {
                    $finalParams['or'][] = [
                        $key,
                        $queryFilters['or'][$key],
                        $value
                    ];
                } elseif (isset($queryFilters['and']) && array_key_exists($key, $queryFilters['and'])) {
                    $finalParams['and'][] = [
                        $key,
                        $queryFilters['and'][$key],
                        $value
                    ];
                }
            }
        }

        return $finalParams;
    }

    /**
     * @return int
     */
    protected function perPage($model)
    {
        return $model->perPage ?? 15;
    }

    /**
     * @param array $data
     * @param array $additionalData
     * @return array
     */
    protected function prepareFromFillable($data, $additionalData)
    {
        $fillable = $this->getModel()->getFillable();
        $finalData = [];
        if (isset($fillable, $data)) {
            foreach ($fillable as $column) {
                if (array_key_exists($column, $data)) {
                    $finalData[$column] = $data[$column];
                } elseif (isset($additionalData, $additionalData[$column])) {
                    $finalData[$column] = $additionalData[$column];
                }
            }
        }
        return $finalData;
    }

    /**
     * @param Model $model
     * @return Model $model
     */
    protected function prepareItem($model)
    {
        return $model;
    }

    /**
     * @param Builder $list
     * @param array $params
     * @return Builder
     */
    protected function prepareList($list, $params)
    {
        return $list;
    }
}

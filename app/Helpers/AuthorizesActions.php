<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

trait AuthorizesActions
{

    /**
     * @param string $ability
     * @param string|Model $model
     * @return Resposta
     */
    protected function authorize($ability, $model): Resposta
    {
        if (!App::runningInConsole() && policy(is_string($model) ? class_basename($model) : $model)) {
            try {
                Gate::authorize($ability, $model);
                return Resposta::object(true, "Autorizado", null, null);
            } catch (Exception $erro) {
                return Resposta::object(false, "Não autorizado a realizar essa ação", null, null);
            }
        }
        return Resposta::object(true, "Autorizado", null, null);
    }
}

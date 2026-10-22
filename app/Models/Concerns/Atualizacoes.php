<?php

namespace App\Models\Concerns;

trait Atualizacoes
{
    public const CREATED_AT = 'criado_em';
    public const UPDATED_AT = null; // null porque não existe tabela
    public const DELETED_AT = 'eliminado_em'; // -> usado somente no User
}

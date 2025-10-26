<?php

/*
|--------------------------------------------------------------------------
| Caso de Prueba
|--------------------------------------------------------------------------
|
| El closure que proporcionas a tus funciones de prueba siempre está vinculado a una clase
| específica de PHPUnit. Por defecto, esa clase es "PHPUnit\Framework\TestCase". Por supuesto,
| puedes necesitar cambiarla usando la función "pest()" para vincular diferentes clases o traits.
|
*/

pest()->extend(Tests\TestCase::class)
 // ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectativas
|--------------------------------------------------------------------------
|
| Cuando estás escribiendo pruebas, a menudo necesitas verificar que los valores cumplan
| ciertas condiciones. La función "expect()" te da acceso a un conjunto de métodos de
| "expectativas" que puedes usar para afirmar diferentes cosas. Por supuesto, puedes
| extender la API de Expectation en cualquier momento.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Funciones
|--------------------------------------------------------------------------
|
| Aunque Pest es muy poderoso por defecto, puedes tener algún código de prueba específico
| para tu proyecto que no quieres repetir en cada archivo. Aquí también puedes exponer
| helpers como funciones globales para ayudarte a reducir el número de líneas de código
| en tus archivos de prueba.
|
*/

function something()
{
    // Función auxiliar para pruebas
}

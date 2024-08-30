<?php

namespace Tests\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

/**
 * Class ControllerTest
 *
 * @package Tests\Http\Controllers
 */
abstract class ControllerTest extends TestCase
{
    protected function index(string $route, array $data = [])
    {
        return $this->signIn()->getJson(route($route, $data));
    }

    protected function show(string $route, Model $model)
    {
        return $this->signIn()->getJson(route($route, $model));
    }

    protected function destroy(string $route, Model $model)
    {
        return $this->signIn()->deleteJson(route($route, $model));
    }

    protected function store(string $route, array $data = [], array $routeParameters = [])
    {
        return $this->signIn()->postJson(route($route, $routeParameters), $data);
    }

    protected function update(string $route, Model $model, array $data = [])
    {
        return $this->signIn()->patchJson(route($route, $model), $data);
    }
}

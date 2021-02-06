<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand as Command;

class MakeUseCaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:useCase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make UseCase';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'UseCase';

    /**
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/usecase.stub';
    }

    /**
     * クラスのデフォルトの名前空間を取得する
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\UseCases';
    }
}

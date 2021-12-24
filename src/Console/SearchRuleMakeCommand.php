<?php

namespace ScoutElastic\Console;

use Illuminate\Console\GeneratorCommand;

class SearchRuleMakeCommand extends GeneratorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'make:search-rule';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Create a new search rule';

    /**
     * {@inheritdoc}
     */
    protected $type = 'Rule';

    /**
     * {@inheritdoc}
     */
    public function getStub(): string
    {
        return __DIR__.'/stubs/search_rule.stub';
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput(): string
    {
        $name = trim($this->argument('name'));
        return "Elasticsearch/SearchRules/$name";
    }
}

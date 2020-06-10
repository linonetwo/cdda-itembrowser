<?php

class Mapgen implements Robbo\Presenter\PresentableInterface
{
    use MagicModel;

    protected $data;
    protected $repo;

    public function __construct(Repositories\RepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function load($data)
    {
        $this->data = $data;
    }

    public function getPresenter()
    {
        return new Presenters\Mapgen($this);
    }

    public function getName() {
        $name = $this->data->name;
        if (is_object($name)) {
            $name = $name->str;
        }
        return $name;
    }
}


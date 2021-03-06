<?php

namespace Recca0120\LaravelPayum\Storage;

use Closure;
use Payum\Core\Model\Identity;
use Payum\Core\Storage\AbstractStorage;

class EloquentStorage extends AbstractStorage
{
    /**
     * $modelResolver.
     *
     * @var \Closure
     */
    protected $modelResolver;

    /**
     * @param string $modelClass
     */
    public function __construct($modelClass)
    {
        parent::__construct($modelClass);
        $this->modelResolver = function () {
            return new $this->modelClass();
        };
    }

    /**
     * setModelResolver.
     *
     * @return static
     */
    public function setModelResolver(Closure $closure)
    {
        $this->modelResolver = $closure;
    }

    /**
     * create.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create()
    {
        $resolver = $this->modelResolver;

        return $resolver();
    }

    /**
     * doUpdateModel.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    protected function doUpdateModel($model)
    {
        $model->save();
    }

    /**
     * doDeleteModel.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    protected function doDeleteModel($model)
    {
        $model->delete();
    }

    /**
     * doGetIdentity.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Payum\Core\Model\Identity
     */
    protected function doGetIdentity($model)
    {
        return new Identity($model->getKey(), $model);
    }

    /**
     * doFind.
     *
     * @param mixed $id
     * @return object|null
     */
    protected function doFind($id)
    {
        return $this->create()->find($id);
    }

    /**
     * findBy.
     *
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria)
    {
        $model = $this->create();
        $query = $model->newQuery();
        foreach ($criteria as $name => $value) {
            $query = $query->where($name, '=', $value);
        }

        return $query->get()->all();
    }
}

<?php namespace Wiz\Blog\Components;

use Flash;
use Redirect;
use Validator;
use Cms\Classes\ComponentBase;

class Search extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Búsqueda',
            'description' => 'Búsqueda de entradas.'
        ];
    }

    public function defineProperties()
    {
        return [
            'minimumLength' => [
                'title'       => 'Miníma cantidad de carácteres',
                'description' => '',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'default'           => '4'
            ]
        ];
    }

    public function onSearch()
    {
        $minimumLength = $this->property('minimumLength');
        $data = post();
        $rules = [
            'q' => 'required|min:' . $minimumLength,
        ];
        $messages = [
            'q.required' => 'Porfavor ingrese el criterio de búsqueda',
            'q.min' => 'El criterio de búsqueda debe tener al menos ' . $minimumLength . ' caracteres.'
        ];
        $validation = Validator::make($data, $rules, $messages);
        if ($validation->fails()) {
            throw new \ValidationException($validation);
        }
        return Redirect::to('blog/buscar/?q=' .  $data['q']);
    }
}

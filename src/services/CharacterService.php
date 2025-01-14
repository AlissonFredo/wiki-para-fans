<?php

namespace app\services;

use app\models\CharacterModel;
use app\repositories\CharacterRepository;

class CharacterService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new CharacterRepository;
    }

    public function create($request)
    {
        $character = new CharacterModel();

        $character->setName($request['name']);
        $character->setDescription($request['description']);
        $character->setPlaceOfBirth($request['placeOfBirth']);
        $character->setOccupation($request['occupation']);
        $character->setFruit($request['fruit']);

        if ($character->existsErrors()) {
            return ['code' => 400, 'errors' => $character->getErrors()];
        }

        $characterId = $this->repository->create($character);

        if (!$characterId) {
            return ['code' => 500];
        }

        $response = $this->repository->show($characterId);

        if (!$response) {
            return ['code' => 500];
        }

        return ['code' => 201, 'data' => $response->toArray()];
    }

    public function update($request, $characterId)
    {
        if (!isset($characterId) || $characterId === '') {
            return ['code' => 400, 'errors' => array(['id' => 'Id is a required attribute'])];
        }

        $character = new CharacterModel();

        $character->setName($request['name']);
        $character->setDescription($request['description']);
        $character->setPlaceOfBirth($request['placeOfBirth']);
        $character->setOccupation($request['occupation']);
        $character->setFruit($request['fruit']);

        if ($character->existsErrors()) {
            return ['code' => 400, 'errors' => $character->getErrors()];
        }

        if (!$this->repository->update($character, $characterId)) {
            return ['code' => 500];
        }

        $response = $this->repository->show($characterId);

        if (!$response) {
            return ['code' => 500];
        }

        return ['code' => 200, 'data' => $response->toArray()];
    }

    public function list()
    {
        $response = $this->repository->list();

        if (!$response) {
            return ['code' => 500];
        }

        if (count($response) > 0) {
            $response = array_map(fn($item) => $item->toArray(), $response);
        }

        return ['code' => 200, 'data' => $response];
    }

    public function showById($id)
    {
        if (!isset($id) || $id === '') {
            return ['code' => 400, 'errors' => array(['id' => 'Id is a required attribute'])];
        }

        $response = $this->repository->show($id);

        if ($response) {
            return ['code' => 200, 'data' => $response->toArray()];
        }

        return ['code' => 500];
    }

    public function destroy($id)
    {
        if (!isset($id) || $id === '') {
            return [
                'code' => 400,
                'errors' => array(['id' => 'Id is a required attribute'])
            ];
        }

        $response = $this->repository->destroy($id);

        if ($response) {
            return ['code' => 200, 'data' => $response];
        }

        return ['code' => 500];
    }

    public function searchByName($name)
    {
        if (!isset($name) || $name === '') {
            return [
                'code' => 400,
                'errors' => array(['name' => 'Name is a required attribute'])
            ];
        }

        $response = $this->repository->searchByName($name);

        if (!$response) {
            return ['code' => 500];
        }

        if (count($response) > 0) {
            $response = array_map(fn($item) => $item->toArray(), $response);
        }

        return ['code' => 200, 'data' => $response];
    }
}

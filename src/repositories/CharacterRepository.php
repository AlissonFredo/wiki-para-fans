<?php

namespace app\repositories;

use PDO;
use app\core\Database;
use app\models\CharacterModel;

class CharacterRepository
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($character)
    {
        try {
            $query = "INSERT INTO characters (
                name,
                description,
                place_of_birth,
                occupations,
                fruit
            ) VALUES (
                :name,
                :description,
                :place_of_birth,
                :occupations,
                :fruit
            )";

            $stmt = $this->conn->prepare($query);

            $stmt->bindValue(":name", $character->getName());
            $stmt->bindValue(":description", $character->getDescription());
            $stmt->bindValue(":place_of_birth", $character->getPlaceOfBirth());
            $stmt->bindValue(":occupations", $character->getOccupation());
            $stmt->bindValue(":fruit", $character->getFruit());

            if (!$stmt->execute()) {
                return false;
            }

            return $this->conn->lastInsertId();
        } catch (\PDOException $exception) {
            error_log(
                "CharacterRepository: error creating new character - {$exception->getMessage()} \n",
                3,
                __DIR__ . "/../../log/error.log"
            );

            return false;
        }
    }

    public function list()
    {
        try {
            $query = "SELECT * FROM characters";
            $stmt = $this->conn->query($query);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) == 0) {
                return $results;
            }

            $results = array_map(fn($result) => $this->toCharacter($result), $results);

            return $results;
        } catch (\PDOException $exception) {
            error_log(
                "CharacterRepository: error fetching all characters - {$exception->getMessage()} \n",
                3,
                __DIR__ . "/../../log/error.log"
            );

            return false;
        }
    }

    public function show($id)
    {
        try {
            $query = "SELECT * FROM characters WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return false;
            }

            return $this->toCharacter($result);
        } catch (\PDOException $exception) {
            error_log(
                "CharacterRepository: error searching for character by id - {$exception->getMessage()} \n",
                3,
                __DIR__ . "/../../log/error.log"
            );

            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $query = "DELETE FROM characters WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":id", $id);

            if ($stmt->execute()) {
                return $stmt->rowCount() > 0;
            }

            return false;
        } catch (\PDOException $exception) {
            error_log(
                "CharacterRepository: error deleting character - {$exception->getMessage()} \n",
                3,
                __DIR__ . "/../../log/error.log"
            );

            return false;
        }
    }

    public function update($character, $id)
    {
        try {
            $query = "UPDATE characters SET 
                    name = :name,
                    description = :description,
                    place_of_birth = :place_of_birth,
                    occupations = :occupations,
                    fruit = :fruit
                WHERE id = :id
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(":id", $id);
            $stmt->bindValue(":name", $character->getName());
            $stmt->bindValue(":description", $character->getDescription());
            $stmt->bindValue(":place_of_birth", $character->getPlaceOfBirth());
            $stmt->bindValue(":occupations", $character->getOccupation());
            $stmt->bindValue(":fruit", $character->getFruit());

            return $stmt->execute();
        } catch (\PDOException $exception) {
            error_log(
                "CharacterRepository: error updating character - {$exception->getMessage()} \n",
                3,
                __DIR__ . "/../../log/error.log"
            );

            return false;
        }
    }

    public function searchByName($name)
    {
        try {
            $query = "SELECT * FROM characters WHERE name LIKE :name";

            $stmt = $this->conn->prepare($query);

            $name = "%{$name}%";
            $stmt->bindValue(":name", $name);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) == 0) {
                return $results;
            }

            $results = array_map(fn($result) => $this->toCharacter($result), $results);

            return $results;
        } catch (\PDOException $exception) {
            error_log(
                "CharacterRepository: error searching for character by name - {$exception->getMessage()} \n",
                3,
                __DIR__ . "/../../log/error.log"
            );

            return false;
        }
    }

    private function toCharacter($array)
    {
        $character = new CharacterModel();

        $character->setId($array['id']);
        $character->setName($array['name']);
        $character->setDescription($array['description']);
        $character->setPlaceOfBirth($array['place_of_birth']);
        $character->setOccupation($array['occupations']);
        $character->setFruit($array['fruit']);
        $character->setCreatedAt($array['created_at']);
        $character->setUpdatedAt($array['updated_at']);

        return $character;
    }
}

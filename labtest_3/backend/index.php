<?php
require 'vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

// Include your database connection class
require_once 'config.php';

// Create App
$app = AppFactory::create();

// Add error middleware
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Add CORS middleware
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8080')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Add OPTIONS route for preflight requests
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

// Get all users
$app->get('/users', function (Request $request, Response $response, $args) {
    $sql = "SELECT * FROM users";
    try {
        $db = new db();
        $db = $db->connect();
        $stmt = $db->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $error = array('error' => $e->getMessage());
        $response->getBody()->write(json_encode($error));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// Get single user
$app->get('/users/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $sql = "SELECT * FROM users WHERE id = :id";
    try {
        $db = new db();
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        if ($user) {
            $response->getBody()->write(json_encode($user));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    } catch (PDOException $e) {
        $error = array('error' => $e->getMessage());
        $response->getBody()->write(json_encode($error));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// Add user
$app->post('/users', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $name = $data['name'];
    $email = $data['email'];
    $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
    try {
        $db = new db();
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $db = null;
        $response->getBody()->write(json_encode(['message' => 'User created successfully']));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $error = array('error' => $e->getMessage());
        $response->getBody()->write(json_encode($error));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// Update user
$app->put('/users/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $data = $request->getParsedBody();
    $name = $data['name'];
    $email = $data['email'];
    $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
    try {
        $db = new db();
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $db = null;
        $response->getBody()->write(json_encode(['message' => 'User updated successfully']));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $error = array('error' => $e->getMessage());
        $response->getBody()->write(json_encode($error));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// Delete user
$app->delete('/users/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $sql = "DELETE FROM users WHERE id = :id";
    try {
        $db = new db();
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $db = null;
        $response->getBody()->write(json_encode(['message' => 'User deleted successfully']));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $error = array('error' => $e->getMessage());
        $response->getBody()->write(json_encode($error));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// Run app
$app->run();
<?php

namespace App\Controllers;

use App\Models\Post;
use Respect\Validation\Validator as v;

class PostController extends Controller {

    private function localValidate($request): bool {
        $validation = $this->container->validator->validate($request, [
            'title' => v::notEmpty()->stringType()->length(1, 50),
            'description' => v::notEmpty()->stringType(),
        ]);

        if ($validation->failed()) {
            return false;
        }

        // Validações específicas

        $title = trim($request->getParam('title'));

        // Evita título só com espaços
        if (empty($title)) {
            $this->container->flash->addMessage('error', 'O título não pode ser vazio.');
            return false;
        }

        // Verifica se já existe post com mesmo título (UNIQUE no banco)
        $postExists = Post::where('title', $title)->first();

        if ($postExists) {
            $this->container->flash->addMessage('error', 'Já existe um post com esse título.');
            return false;
        }

        return true;
    }

    public function create($request,$response){
        if($request->isGet()){
            $old = $_SESSION['old'] ?? [];
            unset($_SESSION['old']); // limpa depois de usar
            return $this->container->view->render($response,'post/create.twig', ['old' => $old]);
        }

        $ok_register = $this->localValidate($request);

        if(!$ok_register){
            $_SESSION['old'] = [
                'title' => $request->getParam('title'),
                'description' => $request->getParam('description'),
                'status' => $request->getParam('status'),
                'featured' => $request->getParam('featured'),
            ];
            return $response->withRedirect($this->container->router->pathFor('post.create'));
        }

        Post::create([
            'title' => $request->getParam('title'),
            'description' => $request->getParam('description'),
            'user_id' => $this->container->auth->user()->id,
        ]);

        return $response->withRedirect($this->container->router->pathFor('home'));
    }
};
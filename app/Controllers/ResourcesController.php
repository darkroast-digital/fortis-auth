<?php

namespace App\Controllers;

use App\Controllers\Controller;

class ResourcesController extends Controller
{
    public function index($request, $response, $args)
    {
        $resources = $this->resources->index()[0];
        $categories = $this->resources->categories();

        return $this->view->render($response, 'dashboard/resources/index.twig', compact('resources', 'categories'));
    }

    public function create($request, $response, $args)
    {
        //
    }

    public function store($request, $response, $args)
    {
        $this->resources->upload($request->getParam('category'), $_FILES['files']);

        $this->flash->addMessage('info', 'Resource Added!');

        return $response->withRedirect($this->router->pathFor('dashboard.resources'));
    }

    public function show($request, $response, $args)
    {
        $content = preg_grep('/^([^.])/', scandir(__DIR__ . '/../../assets/uploads/resources/' . $args['directory']));
        unset($content[0]);
        unset($content[1]);

        return $this->view->render($response, 'dashboard/resources/index.twig', compact('content'));
    }

    public function edit($request, $response, $args)
    {
        //
    }

    public function update($request, $response, $args)
    {
        //
    }

    public function destroy($request, $response, $args)
    {
        $this->resources->delete($request->getParam('resource'));

        $this->flash->addMessage('error', 'Resource Deleted!');

        return $response->withRedirect($this->router->pathFor('dashboard.resources'));
    }
}


<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;

class UsersController extends Controller
{
    public function index($request, $response, $args)
    {
        $users = User::paginate(10);

        return $this->view->render($response, 'dashboard/users/index.twig', compact('users'));
    }

    public function create($request, $response, $args)
    {
        return $this->view->render($response, 'dashboard/users/create.twig');
    }

    public function store($request, $response, $args)
    {
        $params = $request->getParams();
        $password = $params['password'];

        $role = 'user';

        if (isset($params['admin'])) {
            $role = 'admin';
        }

        $user = User::create([
            'name' => $params['name'],
            'password' => password_hash($params['password'], PASSWORD_DEFAULT),
            'position' => $params['position'],
            'email' => $params['email'],
            'phone' => $params['phone'],
            'role' => $role,
        ]);

        $this->mail->from('josh@darkroast.co', 'Fortis Group')
                  ->to([
                        [
                            'name' => $user->name,
                            'email' => $user->email
                        ],
                  ])
                  ->subject('A Post Has Been Editied.')
                  ->send('mail/user.twig', compact('user', 'password'));

        $files = $_FILES;
        $image = $files['featured'];

        if (!file_exists(__DIR__ . '/../../assets/uploads/avatars/' . $user->name)) {
            mkdir(__DIR__ . '/../../assets/uploads/avatars/' . $user->name);
            $user->avatar = $user->name;
            $user->save();
        };

        move_uploaded_file($image['tmp_name'], __DIR__ . '/../../assets/uploads/avatars/' . $user->name . '/avatar.jpg');

        $this->flash->addMessage('info', 'User Added!');
        return $response->withRedirect($this->router->pathFor('dashboard.users'));
    }

    public function show($request, $response, $args)
    {
        //
    }

    public function edit($request, $response, $args)
    {
        $user = User::where('id', $args['id'])->first();

        $avatar = null;

        if (file_exists(__DIR__ . '/../../assets/uploads/avatars/' . $user->name . '/avatar.jpg')) {
            $avatar = '/assets/uploads/avatars/' . $user->name . '/avatar.jpg';
            $user->avatar = $user->name;
            $user->save();
        }

        return $this->view->render($response, 'dashboard/users/edit.twig', compact('user', 'avatar'));
    }

    public function update($request, $response, $args)
    {
        //
    }

    public function destroy($request, $response, $args)
    {
        $user = User::find($args['id']);

        $user->delete();

        $this->flash->addMessage('error', 'User Deleted!');
        return $response->withRedirect($this->router->pathFor('dashboard.users'));
    }
}


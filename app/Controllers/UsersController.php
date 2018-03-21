<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use Mailgun\Mailgun;

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

        $files = $_FILES;
        $image = $files['featured'];

        $role = 'user';

        if (isset($params['admin'])) {
            $role = 'admin';
        }

        $avatar = 'default';

        if ($image['name'] !== '') {
            $avatar = $params['name'];
        }

        $user = User::create([
            'name' => $params['name'],
            'password' => password_hash($params['password'], PASSWORD_DEFAULT),
            'position' => $params['position'],
            'email' => $params['email'],
            'phone' => $params['phone'],
            'role' => $role,
            'avatar' => $avatar
        ]);

        $mg = Mailgun::create('key-430a3c205c21f327abdf2db317f386f2');

        $mg->messages()->send('fortisgroup.ca', [
          'from'    => 'communication@fortisgroup.ca',
          'to'      => $user->email,
          'subject' => 'Introducing the NEW Fortis Group Employee Portal',
          'html'    => $this->view->fetch('mail/welcome.twig', compact('user', 'password'))
        ]);

        if ($image['name'] !== '') {

            if (!file_exists(__DIR__ . '/../../assets/uploads/avatars/' . $request->getParam('name'))) {
                mkdir(__DIR__ . '/../../assets/uploads/avatars/' . $request->getParam('name'));
                $user->avatar = $request->getParam('name');
                $user->save();
            };

            move_uploaded_file($image['tmp_name'], __DIR__ . '/../../assets/uploads/avatars/' . $user->name . '/avatar.jpg');
        }


        $this->flash->addMessage('info', 'User Added!');
        return $response->withRedirect($this->router->pathFor('dashboard.users'));
    }

    public function edit($request, $response, $args)
    {
        $user = User::where('id', $args['id'])->first();

        $avatar = null;

        if (file_exists(__DIR__ . '/../../assets/uploads/avatars/' . $user->name . '/avatar.jpg')) {
            $avatar = '/assets/uploads/avatars/' . $user->name . '/avatar.jpg';
        }

        return $this->view->render($response, 'dashboard/users/edit.twig', compact('user', 'avatar'));
    }

    public function update($request, $response, $args)
    {

        $user = User::find($args['id']);

        $user->name = $request->getParam('name');
        $user->email = $request->getParam('email');

        if ($request->getParam('password') != '') {
            $user->password = password_hash($request->getParam('password'), PASSWORD_DEFAULT);
        }

        $user->phone = $request->getParam('phone');

        $user->role = 'user';

        if ($request->getParam('admin') == 'on') {
            $user->role = 'admin';
        }

        $user->position = $request->getParam('position');

        $files = $_FILES;
        $image = $files['featured'];

        $avatar = 'default';

        if ($image['name'] !== '') {
            $avatar = $request->getParam('name');
        }

        if ($image['name'] !== '') {
            if (!file_exists(__DIR__ . '/../../assets/uploads/avatars/' . $user->name)) {
                mkdir(__DIR__ . '/../../assets/uploads/avatars/' . $user->name);
                $user->avatar = $avatar;
            };

            move_uploaded_file($image['tmp_name'], __DIR__ . '/../../assets/uploads/avatars/' . $user->name . '/avatar.jpg');
        }

        $user->save();

        $this->flash->addMessage('info', 'Profile Updated!');

        if ($user->role == 'admin') {
            return $response->withRedirect($this->router->pathFor('dashboard.users.edit', [
                'id' => $user->id,
            ]));
        } else {
            return $response->withRedirect($this->router->pathFor('dashboard.users.profile', [
                'id' => $user->id,
            ]));
        }
    }

    public function destroy($request, $response, $args)
    {
        $user = User::find($args['id']);

        $user->delete();

        $this->flash->addMessage('error', 'User Deleted!');
        return $response->withRedirect($this->router->pathFor('dashboard.users'));
    }
}


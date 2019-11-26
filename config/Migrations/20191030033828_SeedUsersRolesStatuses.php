<?php

use Migrations\AbstractMigration;

class SeedUsersRolesStatuses extends AbstractMigration
{
    public function up()
    {
        $this->seedUsers();
        $this->seedRoles();
        $this->seedRolesUsers();
        $this->seedUserStatuses();
    }

    public function down()
    {
    }

    public function seedUsers()
    {
        $data = [];
        $levels = [
            'SuperAdmin',
            'Admin',
            'SuperUser',
            'User',
            'Manager',
            'Supervisor',
            'Operator'
        ];

        foreach ($levels as $level) {
            $data[] = [
                'created' => gmdate("Y-m-d H:i:s"),
                'modified' => gmdate("Y-m-d H:i:s"),
                'email' => $level . '@localhost.com',
                'username' => $level,
                'password' => 'secret',
                'first_name' => $level,
                'last_name' => $level,
                'address_1' => '',
                'address_2' => '',
                'suburb' => '',
                'state' => '',
                'post_code' => '',
                'country' => null,
                'mobile' => '',
                'phone' => '',
                'activation' => null,
                'expiration' => null,
                'is_confirmed' => true,
                'user_statuses_id' => 1,
                'password_expiry' => null
            ];
        }

        if (!empty($data)) {
            $table = $this->table('users');
            $table->insert($data)->save();
        }
    }

    public function seedRoles()
    {
        $data = [
            0 => [
                'created' => gmdate("Y-m-d H:i:s"),
                'modified' => gmdate("Y-m-d H:i:s"),
                'name' => 'SuperAdmin',
                'description' => 'Has access to everything',
                'alias' => 'superadmin',
                'session_timeout' => '10'
            ],
            1 => [
                'created' => gmdate("Y-m-d H:i:s"),
                'modified' => gmdate("Y-m-d H:i:s"),
                'name' => 'Admin',
                'description' => 'Has access to most things',
                'alias' => 'admin',
                'session_timeout' => '10'
            ],
            2 => [
                'created' => gmdate("Y-m-d H:i:s"),
                'modified' => gmdate("Y-m-d H:i:s"),
                'name' => 'SuperUser',
                'description' => 'Has the ability to order Jobs and see other User\'s jobs',
                'alias' => 'superuser',
                'session_timeout' => '20'
            ],
            3 => [
                'created' => gmdate("Y-m-d H:i:s"),
                'modified' => gmdate("Y-m-d H:i:s"),
                'name' => 'User',
                'description' => 'Has the ability to order Jobs',
                'alias' => 'user',
                'session_timeout' => '20'
            ],
            4 => [
                'created' => gmdate("Y-m-d H:i:s"),
                'modified' => gmdate("Y-m-d H:i:s"),
                'name' => 'Manager',
                'description' => 'Back Office Manager',
                'alias' => 'manager',
                'session_timeout' => '10'
            ],
            5 => [
                'created' => gmdate("Y-m-d H:i:s"),
                'modified' => gmdate("Y-m-d H:i:s"),
                'name' => 'Supervisor',
                'description' => 'Back Office Supervisor',
                'alias' => 'supervisor',
                'session_timeout' => '10'
            ],
            6 => [
                'created' => gmdate("Y-m-d H:i:s"),
                'modified' => gmdate("Y-m-d H:i:s"),
                'name' => 'Operator',
                'description' => 'Back Office Operator',
                'alias' => 'operator',
                'session_timeout' => '10'
            ]
        ];

        if (!empty($data)) {
            $table = $this->table('roles');
            $table->insert($data)->save();
        }
    }

    public function seedRolesUsers()
    {
        $data = [
            [
                'user_id' => 1,
                'role_id' => 1
            ],
            [
                'user_id' => 2,
                'role_id' => 2
            ],
            [
                'user_id' => 3,
                'role_id' => 3
            ],
            [
                'user_id' => 4,
                'role_id' => 4
            ],
            [
                'user_id' => 5,
                'role_id' => 5
            ],
            [
                'user_id' => 6,
                'role_id' => 6
            ],
            [
                'user_id' => 7,
                'role_id' => 7
            ]
        ];

        if (!empty($data)) {
            $table = $this->table('roles_users');
            $table->insert($data)->save();
        }
    }

    public function seedUserStatuses()
    {
        $data = [
            0 => [
                'rank' => 1,
                'created' => gmdate("Y-m-d H:i:s"),
                'modified' => gmdate("Y-m-d H:i:s"),
                'name' => 'Active',
                'description' => 'Account is active',
                'alias' => 'active',
                'name_status_icon' => null
            ],
            1 => [
                'rank' => 2,
                'created' => gmdate("Y-m-d H:i:s"),
                'modified' => gmdate("Y-m-d H:i:s"),
                'name' => 'Disabled',
                'description' => 'Account is disabled',
                'alias' => 'disabled',
                'name_status_icon' => null
            ],
            2 => [
                'rank' => 3,
                'created' => gmdate("Y-m-d H:i:s"),
                'modified' => gmdate("Y-m-d H:i:s"),
                'name' => 'Approval Pending',
                'description' => 'Pending approval by an Administrator',
                'alias' => 'pending',
                'name_status_icon' => null
            ],
            3 => [
                'rank' => 4,
                'created' => gmdate("Y-m-d H:i:s"),
                'modified' => gmdate("Y-m-d H:i:s"),
                'name' => 'Approval Rejected',
                'description' => 'Rejected by an Administrator',
                'alias' => 'rejected',
                'name_status_icon' => null
            ],
            4 => [
                'rank' => 5,
                'created' => gmdate("Y-m-d H:i:s"),
                'modified' => gmdate("Y-m-d H:i:s"),
                'name' => 'Banned',
                'description' => 'Banned from the Application',
                'alias' => 'banned',
                'name_status_icon' => null
            ]
        ];

        if (!empty($data)) {
            $table = $this->table('user_statuses');
            $table->insert($data)->save();
        }
    }
}

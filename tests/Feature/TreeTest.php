<?php

namespace Tests\Feature;

use App\Models\Tree;
use App\Models\User;
use App\Policies\TreePolicy;
use Tests\TestCase;

class TreeTest extends TestCase
{
    public function test_user_can_view_their_own_tree(): void
    {
        $user = new User;
        $user->id = 1;

        $tree = new Tree;
        $tree->user_id = 1;

        $policy = new TreePolicy;

        $this->assertTrue($policy->view($user, $tree));
    }

    public function test_user_cannot_view_another_users_tree(): void
    {
        $user = new User;
        $user->id = 1;

        $tree = new Tree;
        $tree->user_id = 2;

        $policy = new TreePolicy;

        $this->assertFalse($policy->view($user, $tree));
    }

    public function test_user_can_care_for_their_own_tree(): void
    {
        $user = new User;
        $user->id = 1;

        $tree = new Tree;
        $tree->user_id = 1;

        $policy = new TreePolicy;

        $this->assertTrue($policy->care($user, $tree));
    }

    public function test_user_cannot_care_for_another_users_tree(): void
    {
        $user = new User;
        $user->id = 1;

        $tree = new Tree;
        $tree->user_id = 2;

        $policy = new TreePolicy;

        $this->assertFalse($policy->care($user, $tree));
    }

    public function test_user_can_create_a_tree(): void
    {
        $user = new User;

        $policy = new TreePolicy;

        $this->assertTrue($policy->create($user));
    }

    public function test_user_can_update_their_own_tree(): void
    {
        $user = new User;
        $user->id = 1;

        $tree = new Tree;
        $tree->user_id = 1;

        $policy = new TreePolicy;

        $this->assertTrue($policy->update($user, $tree));
    }

    public function test_user_can_delete_their_own_tree(): void
    {
        $user = new User;
        $user->id = 1;

        $tree = new Tree;
        $tree->user_id = 1;

        $policy = new TreePolicy;

        $this->assertTrue($policy->delete($user, $tree));
    }

    public function test_user_cannot_update_another_users_tree(): void
    {
        $user = new User;
        $user->id = 1;

        $tree = new Tree;
        $tree->user_id = 2;

        $policy = new TreePolicy;

        $this->assertFalse($policy->update($user, $tree));
    }

    public function test_user_cannot_delete_another_users_tree(): void
    {
        $user = new User;
        $user->id = 1;

        $tree = new Tree;
        $tree->user_id = 2;

        $policy = new TreePolicy;

        $this->assertFalse($policy->delete($user, $tree));
    }
}

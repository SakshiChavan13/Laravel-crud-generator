<?php

use App\Models\User;
use App\Models\Post;
use Symfony\Component\HttpFoundation\Response;

it('returns list of posts', function () {
    $user = User::factory()->create();
    Post::factory(5)->create();
    $response = $this->actingAs($user)->getJson('/api/posts');
    $response->assertStatus(Response::HTTP_OK);
    $response->assertJsonCount(5, 'data');
});

it('returns single data of {{ modelName | lower }}', function () {
    $user = User::factory()->create();
    $model = Post::factory()->create();
    $response = $this->actingAs($user)->getJson('/api/posts/' . $model->id);
    $response->assertStatus(Response::HTTP_OK);
});

it('returns 201 when {{ modelName | lower }} created successfully', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->postJson('/api/posts', [
        // TODO: add valid fields
    ]);
    $response->assertStatus(Response::HTTP_CREATED);
});

it('returns 422 when required fields are missing', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->postJson('/api/posts', []);
    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
});

it('returns 200 when {{ modelName | lower }} updated successfully', function () {
    $user = User::factory()->create();
    $model = Post::factory()->create();
    $response = $this->actingAs($user)->putJson('/api/posts/' . $model->id, [
        // TODO: add updated fields
    ]);
    $response->assertStatus(Response::HTTP_OK);
});

it('returns 204 when {{ modelName | lower }} deleted successfully', function () {
    $user = User::factory()->create();
    $model = Post::factory()->create();
    $response = $this->actingAs($user)->deleteJson('/api/posts/' . $model->id);
    $response->assertStatus(Response::HTTP_NO_CONTENT);
});

it('returns 404 when trying to fetch non-existent {{ modelName | lower }}', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->getJson('/api/posts/999999');
    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

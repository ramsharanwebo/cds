<?php
namespace App\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use Illuminate\Http\Request;

interface CDSUserInterface
{

    public function getAllPaginated(int $page_num = 1, int $perPage = 2, string $sort_by, string $order, string $key): LengthAwarePaginator;

    public function getById(int $id): ?User;

    public function create(Request $request): User;

    public function update(Request $request, int $id): User;

    public function delete(int $id): bool;

    public function getUserLogs(int $user_id);

    public function makeArchived(Request $request, int $user_id):User;

}
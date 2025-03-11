<?php 

namespace App\Repositories;

use App\Models\Book;

interface BookRepositoryInterface
{
    public function getAllBooks();
    public function createBook(array $data);
    public function updateBook(Book $book, array $data);
    public function borrowBook(array $data);
    public function getBorrowByUserId(int $user_id, int $book_id);
}

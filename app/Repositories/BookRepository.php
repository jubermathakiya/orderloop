<?php 

namespace App\Repositories;

use App\Models\Book;
use App\Models\Borrow;

class BookRepository implements BookRepositoryInterface
{
    public function getAllBooks()
    {
        return Book::paginate(10);
    }

    public function createBook($data)
    {
        return Book::create([
            'title' => $data['title'],
            'author' => $data['author'],
        ]);
    }

    public function updateBook(Book $book, $data){
        return $book->update([
            'title' => $data['title'],
            'author' => $data['author'],
        ]);
    }

    public function borrowBook($data){
        return Borrow::create($data);
    }

    public function getBorrowByUserId($user_id, $book_id){
        return Borrow::where('user_id', $user_id)
                ->where('book_id', $book_id)
                ->whereNull('returned_at')
                ->first();
    }
}

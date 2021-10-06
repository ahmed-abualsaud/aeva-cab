<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\StudentRepository;
 
class StudentResolver
{
    private $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->studentRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->studentRepository->update($args);
    }

    public function destroy($_, array $args)
    {
        return $this->studentRepository->destroy($args);
    }
}
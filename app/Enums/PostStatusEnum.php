<?php
  
namespace App\Enums;
 
enum PostStatusEnum:string {
    case Draft = 'draft';
    case Published = 'published';
}
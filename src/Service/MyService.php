<?php 
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

class MyService
{
  private $manager;

  public function __construct(EntityManagerInterface $manager)
  {
    $this->manager = $manager;
  }

  public function getPerson($id = 1)
  {
    $query = $this->manager->createQuery("SELECT p FROM App\Entity\Person p WHERE p.id = {$id}");
    try{
      return $query->getSingleResult();
    } catch(NoResultException $e){
      return null;
    }
  }

  public function getMessage(){
    $msgs = [
      'これは、オリジナルのメッセージ・サービスです。',
      'これは、新しいメッセージです。',
      '・・・あ。すみません、ちょっと居眠りしてました。',
      'はい！ メッセージは、何もありません！',
    ];
    $res = array_rand($msgs);
    return $msgs[$res];
  }
}
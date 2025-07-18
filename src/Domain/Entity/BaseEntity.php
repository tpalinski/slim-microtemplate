<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

// This is a utility Entity superclass, which you are free to extend as you see fit, just hooked up automatic timestamp creation
#[MappedSuperclass, HasLifecycleCallbacks]
abstract class BaseEntity {
  #[Column(name: 'created_at', type: 'datetimetz_immutable', nullable: false)]
  private DateTimeImmutable $created_at;

  #[Column(name: 'updated_at', type: 'datetimetz_immutable', nullable: false)]
  private DateTimeImmutable $updated_at;

  #[PrePersist]
  public function prePersistHook(): void {
	  $now = new DateTimeImmutable();
	  $this->created_at = $now;
	  $this->updated_at = $now;
  }

  #[PreUpdate]
  public function preUpdateHook(): void {
	  $now = new DateTimeImmutable();
	  $this->updated_at = $now;
  }

  public function get_updated_at(): DateTimeImmutable {
	  return $this->updated_at;
  }

  public function get_created_at(): DateTimeImmutable {
	  return $this->created_at;
  }

}



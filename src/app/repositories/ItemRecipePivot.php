<?php

class ItemRepositoryPivot implements ItemRepositoryPivotInterface
{
  protected $item;
  protected $recipe;
  protected $database;

  public function __construct(
    ItemRepositoryCache $item,
    RecipeRepositoryCache $recipe
  ) {
    $this->item = $item;
    $this->recipe = $recipe;
    $this->database = $this->read();
  }

  protected function read()
  {
    error_log("Creating ItemRepository pivot...");
    $database = array();
    foreach($this->recipe->where('') as $recipe)
    {
      if(isset($recipe->result))
      {
        $this->link($database, "recipes", $recipe->result, $recipe);
        if(isset($recipe->book_learn))
        {
          foreach($recipe->book_learn as $learn)
          {
            $this->link($database, "learn", $learn[0], $recipe);
          }
        }
      }
      if(isset($recipe->tools))
      {
        foreach($recipe->tools as $group)
        {
          foreach($group as $tool)
          {
            list($id, $amount) = $tool;
            $this->link($database, "toolFor", $id, $recipe);
          }
        }
      }
      if(isset($recipe->components)) 
      {
        foreach($recipe->components as $group)
        {
          foreach($group as $component)
          {
            list($id, $amount) = $component;
            $this->link($database, "toolFor", $id, $recipe);
          }
        }
      }
    }
    return $database;
  }

  protected function link(&$database, $key, $id, $recipe)
  {
    if($key=="recipes" and $recipe->category=="CC_NONCRAFT")
    {
      $database[$id]->disassembly[] = $recipe->id;
      return;
    }
    if($key=="recipes" and isset($recipe->reversible) and $recipe->reversible=="true")      
    {
      $database[$id]->disassembly[] = $recipe->id;
    }

    if($key=="toolFor")
    {
      $key2 = "toolForCategory.{$recipe->category}";
      if($recipe->category!="CC_NONCRAFT")
        $database[$id]->categories[$recipe->category] = $recipe->category;
      $database[$id]->{$key2}[$recipe->id] = $recipe->id;
    }

    $database[$id]->{$key}[] = $recipe->id;
  }

  public function find($item_id, $type)
  {
    if(!isset($this->database[$item_id]->{$type}))
      return array();

    return $this->database[$item_id]->{$type};
  }
}
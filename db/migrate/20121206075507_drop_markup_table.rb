class DropMarkupTable < ActiveRecord::Migration	
  def up
  	drop_table :markups
  end

  def down
  end
end

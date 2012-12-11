class FixMarkupClassColumnName < ActiveRecord::Migration
  def up
  	rename_column :markups, :class, :className
  end

  def down
  end
end

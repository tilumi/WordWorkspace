class AddOidColumnTooutline < ActiveRecord::Migration
  def up
  	add_column :outlines, :oid, :integer
  end

  def down
  	remove_column :outlines, :oid
  end
end

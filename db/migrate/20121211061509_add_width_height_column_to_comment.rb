class AddWidthHeightColumnToComment < ActiveRecord::Migration
  def change
  	add_column :comments, :width, :integer
    add_column :comments, :height, :integer
  end
end

class RemoveMidAndClassNameFromCommentsAndTextRanges < ActiveRecord::Migration
  def up
  	remove_column :comments, :mid
  	remove_column :comments, :className
  	remove_column :text_ranges, :mid
  end

  def down
  	add_column :comments, :mid, :integer
  	add_column :comments, :className, :string
  	add_column :text_ranges, :mid, :integer
  end
end

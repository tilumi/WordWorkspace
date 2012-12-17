class AddMarkupIdToComments < ActiveRecord::Migration
  def change
  	add_column :comments, :markup_id, :integer	
  end
end

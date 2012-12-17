class RemoveDocumentAndUserForeignKeysFromCommments < ActiveRecord::Migration
  def up
  	remove_column :comments, :user_id
  	remove_column :comments, :document_id
  end

  def down
  	add_column :comments, :user_id, :integer
  	add_column :comments, :document_id, :integer
  end
end

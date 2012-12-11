class AddForeignKeyColumnsToComment < ActiveRecord::Migration
  def change
    add_column :comments, :user_id, :integer
    add_column :comments, :document_id, :integer
  end
end

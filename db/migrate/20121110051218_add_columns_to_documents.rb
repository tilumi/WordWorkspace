class AddColumnsToDocuments < ActiveRecord::Migration
  def change
    add_column :documents, :filepath, :string
    add_column :documents, :title, :string
  end
end

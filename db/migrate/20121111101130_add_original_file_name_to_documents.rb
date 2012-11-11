class AddOriginalFileNameToDocuments < ActiveRecord::Migration
  def change
    add_column :documents, :original_filename, :string
  end
end

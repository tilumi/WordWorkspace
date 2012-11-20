class CreateMarkups < ActiveRecord::Migration
  def change
    create_table :markups do |t|
      t.text :markups_data
      t.integer :document_id
      t.integer :user_id
      t.timestamps
    end
  end
end

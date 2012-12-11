class CreateMarkups < ActiveRecord::Migration
  def change
    create_table :markups do |t|
      t.integer :mid
      t.integer :user_id
      t.integer :document_id
      t.string :class
      t.text :content

      t.timestamps
    end
  end
end

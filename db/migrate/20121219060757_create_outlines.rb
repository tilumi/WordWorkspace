class CreateOutlines < ActiveRecord::Migration
  def change
    create_table :outlines do |t|
      t.integer :user_id
      t.integer :document_id
      t.string :start_paragraph
      t.string :end_paragraph
      t.integer :x
      t.integer :y
      t.integer :width
      t.integer :height
      t.string :className
      t.text :content

      t.timestamps
    end
  end
end

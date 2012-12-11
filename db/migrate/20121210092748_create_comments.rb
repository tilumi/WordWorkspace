class CreateComments < ActiveRecord::Migration
  def change
    create_table :comments do |t|
      t.integer :mid
      t.string :className
      t.integer :x
      t.integer :y
      t.text :content

      t.timestamps
    end
  end
end

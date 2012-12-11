class CreateComments < ActiveRecord::Migration
  def change
    create_table :comments do |t|
      t.int :mid
      t.string :className
      t.int :x
      t.int :y
      t.text :content

      t.timestamps
    end
  end
end

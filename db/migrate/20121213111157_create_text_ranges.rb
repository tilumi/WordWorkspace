class CreateTextRanges < ActiveRecord::Migration
  def change
    create_table :text_ranges do |t|
      t.integer :markup_id
      t.string :start_position
      t.integer :length

      t.timestamps
    end
  end
end

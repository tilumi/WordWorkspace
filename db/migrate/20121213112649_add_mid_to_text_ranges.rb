class AddMidToTextRanges < ActiveRecord::Migration
  def change
  	add_column :text_ranges, :mid, :integer
  end
end

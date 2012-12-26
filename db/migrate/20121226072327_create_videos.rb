class CreateVideos < ActiveRecord::Migration
  def change
    create_table :videos do |t|
      t.integer :document_id
      t.string :path

      t.timestamps
    end
  end
end

class AddColumnToAllowedUser < ActiveRecord::Migration
  def change
    add_column :allowed_users, :facebook, :string
  end
end

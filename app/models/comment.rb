class Comment < ActiveRecord::Base
  attr_accessible :className, :content, :mid, :x, :y, :document, :user, :width, :height, :user_id, :document_id
  belongs_to :document
  belongs_to :user
  accepts_nested_attributes_for :document
  accepts_nested_attributes_for :user
end

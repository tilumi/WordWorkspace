class Comment < ActiveRecord::Base
  attr_accessible :className, :content, :mid, :x, :y, :document, :user
  belongs_to :document
  belongs_to :user
  accepts_nested_attributes_for :document
  accepts_nested_attributes_for :user
end

class Markup < ActiveRecord::Base
  attr_accessible :className, :content, :document_id, :mid, :user_id, :document, :user
  belongs_to :document
  belongs_to :user
  accepts_nested_attributes_for :document
  accepts_nested_attributes_for :user
end

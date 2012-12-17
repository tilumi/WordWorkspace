class Markup < ActiveRecord::Base
  attr_accessible :className, :content, :document_id, :mid, :user_id, :document, :user, :textRanges
  belongs_to :document
  belongs_to :user
  accepts_nested_attributes_for :document
  accepts_nested_attributes_for :user

  has_many :textRanges, :dependent => :delete_all
  has_one :comment, :dependent => :delete
end

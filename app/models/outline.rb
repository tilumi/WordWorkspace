class Outline < ActiveRecord::Base
  attr_accessible :className, :content, :document_id, :end_paragraph, :height, :start_paragraph, :user_id, :width, :x, :y, :oid, :document, :user
  belongs_to :document
  belongs_to :user
  accepts_nested_attributes_for :document
  accepts_nested_attributes_for :user
end

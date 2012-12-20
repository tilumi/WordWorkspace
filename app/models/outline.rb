class Outline < ActiveRecord::Base
  attr_accessible :className, :content, :document_id, :end_paragraph, :height, :start_paragraph, :user_id, :width, :x, :y
end

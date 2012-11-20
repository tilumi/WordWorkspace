class Markup < ActiveRecord::Base
  attr_accessible :markups_data
  belongs_to :document
  belongs_to :user
  
end

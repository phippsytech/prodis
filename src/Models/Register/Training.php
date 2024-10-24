<?php
namespace NDISmate\Models\Register;

use NDISmate\CORE\NewCustomModel;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;

class Training extends NewCustomModel
{
    public function __construct($bean = null)
    {
        parent::__construct($bean ?: R::dispense('trainings'));
        $this->fields = [
            'date' => [v::dateTime('Y-m-d')],
            'course_title' => [v::stringType()],
            'trainer' => [v::stringType()],
            'status' => [v::stringType()],
            'completion_date' => [v::optional(v::dateTime('Y-m-d'))],
            'has_evidence' => [v::boolVal()],
            'user_id' => [v::numericVal()] // log user id on create
        ];
    }

    public function create($data)
    {
        try {
            $bean = parent::create($data);
            if (is_array($data['staff_ids']) && !empty($data['staff_ids'])) {
                foreach ($data['staff_ids'] as $staff_id) {
                    if (is_numeric($staff_id)) {
                        $trainingAssignee = R::dispense('trainingassignees');  
                        $trainingAssignee->training_id = $bean->id;   
                        $trainingAssignee->staff_id = $staff_id;               
                        R::store($trainingAssignee);  
                    }
                }
            }

            R::store($bean);
            return $bean;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function update($data)
    {
        $bean = parent::update($data);

        if ($data['has_evidence'] ==='yes' && $data['status'] !== 'completed') {
            return ['error' => 'Evidence cannot be added for incomplete training.'];
        }

        if (is_array($data['staff_ids']) && !empty($data['staff_ids'])) {
            $existingAssignees = R::find('trainingassignees', 'training_id = ?', [$bean->id]);

            $existingStaffIds = array_map(function($assignee) {
                return $assignee->staff_id;
            }, $existingAssignees);

            foreach ($existingAssignees as $assignee) {
                if (!in_array($assignee->staff_id, $data['staff_ids'])) {
                    R::trash($assignee);
                }
            }

            foreach ($data['staff_ids'] as $staff_id) {
                if (is_numeric($staff_id) && !in_array($staff_id, $existingStaffIds)) {
                    $trainingAssignee = R::dispense('trainingassignees');
                    $trainingAssignee->training_id = $bean->id;
                    $trainingAssignee->staff_id = $staff_id;
                    R::store($trainingAssignee); 
                }
            }
        }

        return $bean;
    }

    public function delete($data) 
    {  
        $trainingBean = R::load('trainings', $data['id']);
            
        if (!$trainingBean->id) {
            return ['error' => 'Training not found.'];
        }
        $assignees = R::find('trainingassignees', 'training_id = ?', [$data['id']]);

        foreach ($assignees as $assignee) {
            R::trash($assignee);
        }

        R::trash($trainingBean);
    }
    

}
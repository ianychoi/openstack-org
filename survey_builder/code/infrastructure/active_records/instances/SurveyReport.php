<?php
/**
 * Copyright 2015 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class SurveyReport
 */
class SurveyReport extends DataObject {

    static $db = array
    (
        'Name' => 'Varchar(254)',
        'Display' => 'Boolean(1)'
    );

    static $has_one = array
    (
        'Template' => 'SurveyTemplate',
    );

    static $has_many = array(
        'Sections' => 'SurveyReportSection',
        'Filters'  => 'SurveyReportFilter',
    );

    private static $summary_fields = array(
        'ID',
        'Template.Title',
    );

    private static $defaults = array(
        'Display' => 1
    );

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return (int)$this->getField('ID');
    }

    public function getSections() {
        $sections = $this->Sections()->sort('Order');
        $section_array = array();
        foreach ($sections as $section) {
            if ($section->Graphs()->count()) {
                $section_array[] = $section->toMap();
            }
        }

        return $section_array;
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $templateList = SurveyTemplate::get()->filter(array('ClassName' => 'SurveyTemplate' ))->sort('Title')->map()->toArray();
        $templateSelect = DropdownField::create('TemplateID', 'Survey Template')->setSource($templateList);
        $fields->replaceField('TemplateID', $templateSelect);

        $sectionList = SurveyReportSection::get()->filter('ReportID',0);
        $config = GridFieldConfig_RelationEditor::create(15);
        $config->addComponent(new GridFieldCopySectionsAction($this->ID));
        $config->removeComponentsByType('GridFieldAddExistingAutocompleter');
        $config->removeComponentsByType('GridFieldDeleteAction');
        $config->addComponent(new GridFieldDeleteAction(false));
        $sections = new GridField('Sections', 'Sections', $this->Sections(), $config);
        $fields->replaceField('Sections', $sections);

        $filterList = SurveyReportFilter::get()->filter('ReportID',0);
        $config = GridFieldConfig_RelationEditor::create(15);
        $config->addComponent(new GridFieldCopyFiltersAction($this->ID));
        $config->removeComponentsByType('GridFieldAddExistingAutocompleter');
        $config->removeComponentsByType('GridFieldDeleteAction');
        $config->addComponent(new GridFieldDeleteAction(false));
        $filters = new GridField('Filters', 'Filters', $this->Filters(), $config);
        $fields->replaceField('Filters', $filters);

        return $fields;
    }

    public function mapTemplate()
    {
        $report_map = array();

        $filters = array();
        foreach ($this->Filters()->sort('Order') as $filter) {
            $options = array();

            if ($filter->Question()->Exists()) {
                foreach ($filter->Question()->Values() as $option) {
                    $options[] = array('id' => $option->ID, 'value' => $option->Value);
                }
            }

            $filters[] = array(
                'Label'    => $filter->Label,
                'Question' => $filter->Question()->ID,
                'Options'  => $options,
            );
        }
        $report_map['Filters'] = $filters;

        $report_map['Sections'] = $this->getSections();

        return $report_map;

    }


}
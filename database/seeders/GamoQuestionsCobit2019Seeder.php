<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GamoObjective;

class GamoQuestionsCobit2019Seeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder populates COBIT 2019 Management Practices for all 40 GAMOs
     * Based on official COBIT 2019 framework documentation
     */
    public function run(): void
    {
        // Clear existing data (disable foreign key checks temporarily)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('gamo_questions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $gamoObjectives = GamoObjective::all()->keyBy('code');

        $practices = $this->getCobit2019Practices();

        foreach ($practices as $gamoCode => $gamoPractices) {
            $gamo = $gamoObjectives->get($gamoCode);
            
            if (!$gamo) {
                $this->command->warn("GAMO not found: {$gamoCode}");
                continue;
            }

            foreach ($gamoPractices as $practice) {
                DB::table('gamo_questions')->insert([
                    'code' => $practice['code'],
                    'gamo_objective_id' => $gamo->id,
                    'question_text' => $practice['question_text'],
                    'maturity_level' => $practice['maturity_level'],
                    'question_type' => 'text',
                    'evidence_requirement' => $practice['evidence_requirement'] ?? null,
                    'guidance' => $practice['guidance'] ?? null,
                    'question_order' => $practice['question_order'] ?? 0,
                    'required' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->command->info("Seeded {$gamoCode} with " . count($gamoPractices) . " practices");
        }

        $this->command->info('COBIT 2019 practices seeded successfully!');
    }

    /**
     * Get all COBIT 2019 Management Practices
     */
    private function getCobit2019Practices(): array
    {
        return [
            // ============================================================
            // EDM - EVALUATE, DIRECT, MONITOR (Governance)
            // ============================================================
            
            'EDM01' => [
                ['code' => 'EDM01.01', 'question_text' => 'Evaluate the governance system', 'maturity_level' => 1, 'evidence_requirement' => 'Governance framework documentation', 'guidance' => 'Review and evaluate governance system effectiveness', 'question_order' => 1],
                ['code' => 'EDM01.02', 'question_text' => 'Direct the governance system', 'maturity_level' => 2, 'evidence_requirement' => 'Governance directives and policies', 'guidance' => 'Provide direction for governance implementation', 'question_order' => 2],
                ['code' => 'EDM01.03', 'question_text' => 'Monitor the governance system', 'maturity_level' => 3, 'evidence_requirement' => 'Monitoring reports and metrics', 'guidance' => 'Continuously monitor governance effectiveness', 'question_order' => 3],
            ],

            'EDM02' => [
                ['code' => 'EDM02.01', 'question_text' => 'Evaluate value optimization', 'maturity_level' => 1, 'evidence_requirement' => 'Value assessment reports', 'guidance' => 'Assess value delivery from IT investments', 'question_order' => 1],
                ['code' => 'EDM02.02', 'question_text' => 'Direct value optimization', 'maturity_level' => 2, 'evidence_requirement' => 'Value optimization strategy', 'guidance' => 'Direct activities to optimize business value', 'question_order' => 2],
                ['code' => 'EDM02.03', 'question_text' => 'Monitor value optimization', 'maturity_level' => 3, 'evidence_requirement' => 'Value metrics dashboard', 'guidance' => 'Monitor value realization continuously', 'question_order' => 3],
            ],

            'EDM03' => [
                ['code' => 'EDM03.01', 'question_text' => 'Evaluate risk management', 'maturity_level' => 1, 'evidence_requirement' => 'Risk assessment documentation', 'guidance' => 'Evaluate risk management practices', 'question_order' => 1],
                ['code' => 'EDM03.02', 'question_text' => 'Direct risk management', 'maturity_level' => 2, 'evidence_requirement' => 'Risk management policies', 'guidance' => 'Provide direction for risk management', 'question_order' => 2],
                ['code' => 'EDM03.03', 'question_text' => 'Monitor risk management', 'maturity_level' => 3, 'evidence_requirement' => 'Risk monitoring reports', 'guidance' => 'Monitor risk management effectiveness', 'question_order' => 3],
            ],

            'EDM04' => [
                ['code' => 'EDM04.01', 'question_text' => 'Evaluate resource optimization', 'maturity_level' => 1, 'evidence_requirement' => 'Resource utilization reports', 'guidance' => 'Assess resource optimization effectiveness', 'question_order' => 1],
                ['code' => 'EDM04.02', 'question_text' => 'Direct resource optimization', 'maturity_level' => 2, 'evidence_requirement' => 'Resource allocation strategy', 'guidance' => 'Direct resource optimization activities', 'question_order' => 2],
                ['code' => 'EDM04.03', 'question_text' => 'Monitor resource optimization', 'maturity_level' => 3, 'evidence_requirement' => 'Resource monitoring dashboard', 'guidance' => 'Monitor resource utilization continuously', 'question_order' => 3],
            ],

            'EDM05' => [
                ['code' => 'EDM05.01', 'question_text' => 'Evaluate stakeholder engagement', 'maturity_level' => 1, 'evidence_requirement' => 'Stakeholder analysis', 'guidance' => 'Evaluate stakeholder communication effectiveness', 'question_order' => 1],
                ['code' => 'EDM05.02', 'question_text' => 'Direct stakeholder engagement', 'maturity_level' => 2, 'evidence_requirement' => 'Communication strategy', 'guidance' => 'Direct stakeholder engagement activities', 'question_order' => 2],
                ['code' => 'EDM05.03', 'question_text' => 'Monitor stakeholder engagement', 'maturity_level' => 3, 'evidence_requirement' => 'Stakeholder feedback reports', 'guidance' => 'Monitor stakeholder satisfaction', 'question_order' => 3],
            ],

            // ============================================================
            // APO - ALIGN, PLAN, ORGANIZE
            // ============================================================

            'APO01' => [
                ['code' => 'APO01.01', 'question_text' => 'Maintain a strategic vision', 'maturity_level' => 1, 'evidence_requirement' => 'Strategic vision document', 'guidance' => 'Define and maintain IT strategic vision'],
                ['code' => 'APO01.02', 'question_text' => 'Assess the current environment and define strategic requirements', 'maturity_level' => 1, 'evidence_requirement' => 'Environmental assessment', 'guidance' => 'Analyze current state and future requirements'],
                ['code' => 'APO01.03', 'question_text' => 'Define the strategic direction and target state', 'maturity_level' => 2, 'evidence_requirement' => 'Strategic plan document', 'guidance' => 'Define desired future state and roadmap'],
                ['code' => 'APO01.04', 'question_text' => 'Communicate the strategic and tactical direction', 'maturity_level' => 2, 'evidence_requirement' => 'Communication records', 'guidance' => 'Disseminate strategic direction to stakeholders'],
                ['code' => 'APO01.05', 'question_text' => 'Plan portfolio of initiatives', 'maturity_level' => 3, 'evidence_requirement' => 'Portfolio plan', 'guidance' => 'Define and prioritize strategic initiatives'],
                ['code' => 'APO01.06', 'question_text' => 'Manage portfolio of initiatives', 'maturity_level' => 3, 'evidence_requirement' => 'Portfolio management reports', 'guidance' => 'Monitor and control portfolio execution'],
                ['code' => 'APO01.07', 'question_text' => 'Maintain an IT strategy and direction statement', 'maturity_level' => 4, 'evidence_requirement' => 'IT strategy document', 'guidance' => 'Document and update IT strategy regularly'],
                ['code' => 'APO01.08', 'question_text' => 'Manage strategic change', 'maturity_level' => 5, 'evidence_requirement' => 'Change management records', 'guidance' => 'Implement and manage strategic changes'],
            ],

            'APO02' => [
                ['code' => 'APO02.01', 'question_text' => 'Understand enterprise direction and business requirements', 'maturity_level' => 1, 'evidence_requirement' => 'Business requirements documentation', 'guidance' => 'Gather and document business needs'],
                ['code' => 'APO02.02', 'question_text' => 'Assess the current capability and performance', 'maturity_level' => 1, 'evidence_requirement' => 'Capability assessment report', 'guidance' => 'Evaluate current IT capabilities'],
                ['code' => 'APO02.03', 'question_text' => 'Define the target capability', 'maturity_level' => 2, 'evidence_requirement' => 'Target capability model', 'guidance' => 'Define desired future capabilities'],
                ['code' => 'APO02.04', 'question_text' => 'Conduct gap analysis', 'maturity_level' => 2, 'evidence_requirement' => 'Gap analysis report', 'guidance' => 'Identify gaps between current and target state'],
                ['code' => 'APO02.05', 'question_text' => 'Define the strategic plan and road map', 'maturity_level' => 3, 'evidence_requirement' => 'Strategic roadmap', 'guidance' => 'Create implementation roadmap'],
                ['code' => 'APO02.06', 'question_text' => 'Communicate the strategy and direction', 'maturity_level' => 3, 'evidence_requirement' => 'Communication plan', 'guidance' => 'Share strategy with stakeholders'],
            ],

            'APO03' => [
                ['code' => 'APO03.01', 'question_text' => 'Establish and maintain an enterprise architecture framework', 'maturity_level' => 1, 'evidence_requirement' => 'EA framework documentation', 'guidance' => 'Define enterprise architecture framework'],
                ['code' => 'APO03.02', 'question_text' => 'Define reference architecture', 'maturity_level' => 2, 'evidence_requirement' => 'Reference architecture models', 'guidance' => 'Create reference architecture patterns'],
                ['code' => 'APO03.03', 'question_text' => 'Select opportunities and solutions', 'maturity_level' => 2, 'evidence_requirement' => 'Solution architecture documents', 'guidance' => 'Identify and evaluate solution options'],
                ['code' => 'APO03.04', 'question_text' => 'Define architecture implementation', 'maturity_level' => 3, 'evidence_requirement' => 'Implementation plans', 'guidance' => 'Plan architecture implementation'],
                ['code' => 'APO03.05', 'question_text' => 'Provide enterprise architecture services', 'maturity_level' => 4, 'evidence_requirement' => 'EA services catalog', 'guidance' => 'Deliver architecture services to projects'],
            ],

            'APO04' => [
                ['code' => 'APO04.01', 'question_text' => 'Create awareness of innovation opportunities', 'maturity_level' => 1, 'evidence_requirement' => 'Innovation tracking log', 'guidance' => 'Monitor technology trends and innovations'],
                ['code' => 'APO04.02', 'question_text' => 'Maintain an understanding of the enterprise environment', 'maturity_level' => 1, 'evidence_requirement' => 'Environmental scan reports', 'guidance' => 'Analyze business and technology environment'],
                ['code' => 'APO04.03', 'question_text' => 'Monitor and scan the technology environment', 'maturity_level' => 2, 'evidence_requirement' => 'Technology radar', 'guidance' => 'Track emerging technologies'],
                ['code' => 'APO04.04', 'question_text' => 'Assess innovation potential', 'maturity_level' => 3, 'evidence_requirement' => 'Innovation assessment', 'guidance' => 'Evaluate innovation business impact'],
                ['code' => 'APO04.05', 'question_text' => 'Recommend appropriate further initiatives', 'maturity_level' => 3, 'evidence_requirement' => 'Innovation recommendations', 'guidance' => 'Propose innovation implementation'],
                ['code' => 'APO04.06', 'question_text' => 'Monitor implementation and use of innovation', 'maturity_level' => 4, 'evidence_requirement' => 'Innovation tracking metrics', 'guidance' => 'Track innovation adoption and benefits'],
            ],

            'APO05' => [
                ['code' => 'APO05.01', 'question_text' => 'Establish the target investment mix', 'maturity_level' => 1, 'evidence_requirement' => 'Investment portfolio strategy', 'guidance' => 'Define optimal investment allocation'],
                ['code' => 'APO05.02', 'question_text' => 'Determine the availability and sources of funds', 'maturity_level' => 2, 'evidence_requirement' => 'Budget documentation', 'guidance' => 'Identify funding sources and constraints'],
                ['code' => 'APO05.03', 'question_text' => 'Evaluate and select programs to fund', 'maturity_level' => 2, 'evidence_requirement' => 'Investment evaluation criteria', 'guidance' => 'Assess and prioritize investment proposals'],
                ['code' => 'APO05.04', 'question_text' => 'Monitor, optimize and report on investment portfolio performance', 'maturity_level' => 3, 'evidence_requirement' => 'Portfolio performance reports', 'guidance' => 'Track and optimize portfolio value'],
                ['code' => 'APO05.05', 'question_text' => 'Maintain portfolios', 'maturity_level' => 4, 'evidence_requirement' => 'Portfolio management process', 'guidance' => 'Continuously manage investment portfolio'],
            ],

            'APO06' => [
                ['code' => 'APO06.01', 'question_text' => 'Manage finance and accounting within IT', 'maturity_level' => 1, 'evidence_requirement' => 'Financial records', 'guidance' => 'Implement IT financial management'],
                ['code' => 'APO06.02', 'question_text' => 'Prioritize resource allocation', 'maturity_level' => 2, 'evidence_requirement' => 'Resource allocation plan', 'guidance' => 'Optimize resource distribution'],
                ['code' => 'APO06.03', 'question_text' => 'Create and maintain budgets', 'maturity_level' => 2, 'evidence_requirement' => 'Budget documents', 'guidance' => 'Develop annual IT budgets'],
                ['code' => 'APO06.04', 'question_text' => 'Model and allocate costs', 'maturity_level' => 3, 'evidence_requirement' => 'Cost allocation model', 'guidance' => 'Implement cost allocation methodology'],
                ['code' => 'APO06.05', 'question_text' => 'Manage costs', 'maturity_level' => 3, 'evidence_requirement' => 'Cost management reports', 'guidance' => 'Control and optimize IT costs'],
            ],

            'APO07' => [
                ['code' => 'APO07.01', 'question_text' => 'Maintain adequate and appropriate staffing', 'maturity_level' => 1, 'evidence_requirement' => 'Staffing plan', 'guidance' => 'Ensure appropriate IT workforce'],
                ['code' => 'APO07.02', 'question_text' => 'Identify key IT personnel', 'maturity_level' => 1, 'evidence_requirement' => 'Key personnel registry', 'guidance' => 'Document critical IT roles'],
                ['code' => 'APO07.03', 'question_text' => 'Maintain the skills and competencies of personnel', 'maturity_level' => 2, 'evidence_requirement' => 'Skills matrix and training records', 'guidance' => 'Develop and maintain IT competencies'],
                ['code' => 'APO07.04', 'question_text' => 'Evaluate and assess employee job performance', 'maturity_level' => 3, 'evidence_requirement' => 'Performance reviews', 'guidance' => 'Conduct regular performance evaluations'],
                ['code' => 'APO07.05', 'question_text' => 'Plan and track the usage of IT and business human resources', 'maturity_level' => 3, 'evidence_requirement' => 'Resource utilization reports', 'guidance' => 'Monitor workforce allocation'],
                ['code' => 'APO07.06', 'question_text' => 'Manage contract staff', 'maturity_level' => 4, 'evidence_requirement' => 'Contractor management process', 'guidance' => 'Oversee external workforce'],
            ],

            // ============================================================
            // BAI - BUILD, ACQUIRE, IMPLEMENT
            // ============================================================

            'BAI01' => [
                ['code' => 'BAI01.01', 'question_text' => 'Maintain a standard approach for program and project management', 'maturity_level' => 1, 'evidence_requirement' => 'Project management methodology', 'guidance' => 'Define project management standards'],
                ['code' => 'BAI01.02', 'question_text' => 'Initiate a program or project', 'maturity_level' => 1, 'evidence_requirement' => 'Project charter', 'guidance' => 'Launch projects with proper authorization'],
                ['code' => 'BAI01.03', 'question_text' => 'Manage stakeholder engagement', 'maturity_level' => 2, 'evidence_requirement' => 'Stakeholder management plan', 'guidance' => 'Engage project stakeholders effectively'],
                ['code' => 'BAI01.04', 'question_text' => 'Develop and maintain the program/project plan', 'maturity_level' => 2, 'evidence_requirement' => 'Project plans', 'guidance' => 'Create comprehensive project plans'],
                ['code' => 'BAI01.05', 'question_text' => 'Manage program/project scope', 'maturity_level' => 3, 'evidence_requirement' => 'Scope management records', 'guidance' => 'Control project scope changes'],
                ['code' => 'BAI01.06', 'question_text' => 'Manage program/project phases', 'maturity_level' => 3, 'evidence_requirement' => 'Phase gate documentation', 'guidance' => 'Execute project phase management'],
                ['code' => 'BAI01.07', 'question_text' => 'Manage program/project resources', 'maturity_level' => 3, 'evidence_requirement' => 'Resource management plan', 'guidance' => 'Allocate and manage project resources'],
                ['code' => 'BAI01.08', 'question_text' => 'Manage program/project risks', 'maturity_level' => 4, 'evidence_requirement' => 'Risk register', 'guidance' => 'Identify and mitigate project risks'],
                ['code' => 'BAI01.09', 'question_text' => 'Manage program/project quality', 'maturity_level' => 4, 'evidence_requirement' => 'Quality management plan', 'guidance' => 'Ensure project deliverable quality'],
                ['code' => 'BAI01.10', 'question_text' => 'Manage program/project change', 'maturity_level' => 4, 'evidence_requirement' => 'Change control log', 'guidance' => 'Control project changes'],
                ['code' => 'BAI01.11', 'question_text' => 'Close a program or project', 'maturity_level' => 5, 'evidence_requirement' => 'Project closure report', 'guidance' => 'Formally close completed projects'],
            ],

            'BAI02' => [
                ['code' => 'BAI02.01', 'question_text' => 'Define and maintain business functional and technical requirements', 'maturity_level' => 1, 'evidence_requirement' => 'Requirements documentation', 'guidance' => 'Document business and technical requirements'],
                ['code' => 'BAI02.02', 'question_text' => 'Perform a feasibility study and formulate alternative solutions', 'maturity_level' => 2, 'evidence_requirement' => 'Feasibility study report', 'guidance' => 'Evaluate solution options'],
                ['code' => 'BAI02.03', 'question_text' => 'Manage requirements risk', 'maturity_level' => 2, 'evidence_requirement' => 'Requirements risk register', 'guidance' => 'Identify requirements-related risks'],
                ['code' => 'BAI02.04', 'question_text' => 'Obtain approval of requirements and solutions', 'maturity_level' => 3, 'evidence_requirement' => 'Approval records', 'guidance' => 'Get stakeholder sign-off on requirements'],
            ],

            'BAI03' => [
                ['code' => 'BAI03.01', 'question_text' => 'Design high-level solutions', 'maturity_level' => 1, 'evidence_requirement' => 'High-level design documents', 'guidance' => 'Create solution architecture design'],
                ['code' => 'BAI03.02', 'question_text' => 'Design detailed solution components', 'maturity_level' => 2, 'evidence_requirement' => 'Detailed design specifications', 'guidance' => 'Develop detailed component designs'],
                ['code' => 'BAI03.03', 'question_text' => 'Develop solution components', 'maturity_level' => 2, 'evidence_requirement' => 'Source code and build artifacts', 'guidance' => 'Build or acquire solution components'],
                ['code' => 'BAI03.04', 'question_text' => 'Procure IT components', 'maturity_level' => 3, 'evidence_requirement' => 'Procurement records', 'guidance' => 'Acquire external components'],
                ['code' => 'BAI03.05', 'question_text' => 'Build solutions', 'maturity_level' => 3, 'evidence_requirement' => 'Build documentation', 'guidance' => 'Integrate components into solutions'],
                ['code' => 'BAI03.06', 'question_text' => 'Manage changes to requirements and solutions', 'maturity_level' => 4, 'evidence_requirement' => 'Change requests', 'guidance' => 'Control solution changes'],
                ['code' => 'BAI03.07', 'question_text' => 'Maintain solutions', 'maturity_level' => 5, 'evidence_requirement' => 'Maintenance logs', 'guidance' => 'Perform ongoing solution maintenance'],
            ],

            'BAI04' => [
                ['code' => 'BAI04.01', 'question_text' => 'Plan for operational readiness', 'maturity_level' => 1, 'evidence_requirement' => 'Readiness plan', 'guidance' => 'Prepare for solution deployment'],
                ['code' => 'BAI04.02', 'question_text' => 'Train users', 'maturity_level' => 2, 'evidence_requirement' => 'Training materials and records', 'guidance' => 'Provide user training'],
                ['code' => 'BAI04.03', 'question_text' => 'Test, convert and migrate data', 'maturity_level' => 2, 'evidence_requirement' => 'Data migration plan', 'guidance' => 'Migrate data to new solution'],
                ['code' => 'BAI04.04', 'question_text' => 'Establish service management capability', 'maturity_level' => 3, 'evidence_requirement' => 'Service management procedures', 'guidance' => 'Prepare service management processes'],
                ['code' => 'BAI04.05', 'question_text' => 'Promote to production and manage releases', 'maturity_level' => 3, 'evidence_requirement' => 'Release management records', 'guidance' => 'Deploy solutions to production'],
                ['code' => 'BAI04.06', 'question_text' => 'Provide early production support', 'maturity_level' => 4, 'evidence_requirement' => 'Support logs', 'guidance' => 'Support users post-deployment'],
            ],

            // ============================================================
            // DSS - DELIVER, SERVICE, SUPPORT
            // ============================================================

            'DSS01' => [
                ['code' => 'DSS01.01', 'question_text' => 'Perform operational procedures', 'maturity_level' => 1, 'evidence_requirement' => 'Operational procedures documentation', 'guidance' => 'Execute standard operating procedures'],
                ['code' => 'DSS01.02', 'question_text' => 'Manage outsourced IT services', 'maturity_level' => 2, 'evidence_requirement' => 'Outsourcing contracts and SLAs', 'guidance' => 'Oversee external service providers'],
                ['code' => 'DSS01.03', 'question_text' => 'Monitor IT infrastructure', 'maturity_level' => 2, 'evidence_requirement' => 'Monitoring dashboards', 'guidance' => 'Continuously monitor infrastructure'],
                ['code' => 'DSS01.04', 'question_text' => 'Manage the environment', 'maturity_level' => 3, 'evidence_requirement' => 'Environmental controls', 'guidance' => 'Manage physical environment'],
                ['code' => 'DSS01.05', 'question_text' => 'Manage facilities', 'maturity_level' => 3, 'evidence_requirement' => 'Facilities management logs', 'guidance' => 'Maintain IT facilities'],
            ],

            'DSS02' => [
                ['code' => 'DSS02.01', 'question_text' => 'Define service requests and incidents', 'maturity_level' => 1, 'evidence_requirement' => 'Service request catalog', 'guidance' => 'Classify service requests and incidents'],
                ['code' => 'DSS02.02', 'question_text' => 'Record, classify and prioritize requests and incidents', 'maturity_level' => 1, 'evidence_requirement' => 'Incident management system', 'guidance' => 'Log and categorize all requests'],
                ['code' => 'DSS02.03', 'question_text' => 'Verify, approve and fulfill service requests', 'maturity_level' => 2, 'evidence_requirement' => 'Service fulfillment records', 'guidance' => 'Process service requests'],
                ['code' => 'DSS02.04', 'question_text' => 'Investigate, diagnose and allocate incidents', 'maturity_level' => 2, 'evidence_requirement' => 'Incident investigation logs', 'guidance' => 'Troubleshoot and resolve incidents'],
                ['code' => 'DSS02.05', 'question_text' => 'Resolve and recover from incidents', 'maturity_level' => 3, 'evidence_requirement' => 'Resolution documentation', 'guidance' => 'Restore services after incidents'],
                ['code' => 'DSS02.06', 'question_text' => 'Close service requests and incidents', 'maturity_level' => 3, 'evidence_requirement' => 'Closure records', 'guidance' => 'Complete and document resolutions'],
                ['code' => 'DSS02.07', 'question_text' => 'Track status and produce reports', 'maturity_level' => 4, 'evidence_requirement' => 'Incident reports and metrics', 'guidance' => 'Report on incident management'],
            ],

            'DSS03' => [
                ['code' => 'DSS03.01', 'question_text' => 'Identify and classify problems', 'maturity_level' => 1, 'evidence_requirement' => 'Problem records', 'guidance' => 'Document recurring problems'],
                ['code' => 'DSS03.02', 'question_text' => 'Investigate and diagnose problems', 'maturity_level' => 2, 'evidence_requirement' => 'Root cause analysis', 'guidance' => 'Perform root cause analysis'],
                ['code' => 'DSS03.03', 'question_text' => 'Raise known errors', 'maturity_level' => 2, 'evidence_requirement' => 'Known error database', 'guidance' => 'Document known errors'],
                ['code' => 'DSS03.04', 'question_text' => 'Resolve and close problems', 'maturity_level' => 3, 'evidence_requirement' => 'Problem resolution records', 'guidance' => 'Implement permanent fixes'],
                ['code' => 'DSS03.05', 'question_text' => 'Perform proactive problem management', 'maturity_level' => 4, 'evidence_requirement' => 'Trend analysis reports', 'guidance' => 'Prevent problems proactively'],
            ],

            'DSS04' => [
                ['code' => 'DSS04.01', 'question_text' => 'Define the continuity strategy, objectives and plan', 'maturity_level' => 1, 'evidence_requirement' => 'Business continuity plan', 'guidance' => 'Develop continuity strategy'],
                ['code' => 'DSS04.02', 'question_text' => 'Maintain a continuity strategy and plan', 'maturity_level' => 2, 'evidence_requirement' => 'Updated continuity plans', 'guidance' => 'Keep plans current'],
                ['code' => 'DSS04.03', 'question_text' => 'Maintain backup strategies and plans', 'maturity_level' => 2, 'evidence_requirement' => 'Backup procedures', 'guidance' => 'Ensure data backup capability'],
                ['code' => 'DSS04.04', 'question_text' => 'Test the continuity plan', 'maturity_level' => 3, 'evidence_requirement' => 'Test results', 'guidance' => 'Conduct regular continuity tests'],
                ['code' => 'DSS04.05', 'question_text' => 'Review, maintain and improve the continuity plan', 'maturity_level' => 4, 'evidence_requirement' => 'Plan review records', 'guidance' => 'Continuously improve plans'],
                ['code' => 'DSS04.06', 'question_text' => 'Provide continuity training', 'maturity_level' => 4, 'evidence_requirement' => 'Training records', 'guidance' => 'Train staff on continuity procedures'],
                ['code' => 'DSS04.07', 'question_text' => 'Manage backup and restoration', 'maturity_level' => 3, 'evidence_requirement' => 'Backup logs', 'guidance' => 'Execute backup and restore operations'],
                ['code' => 'DSS04.08', 'question_text' => 'Conduct post-resumption review', 'maturity_level' => 5, 'evidence_requirement' => 'Post-incident reviews', 'guidance' => 'Learn from continuity events'],
            ],

            // ============================================================
            // MEA - MONITOR, EVALUATE, ASSESS
            // ============================================================

            'MEA01' => [
                ['code' => 'MEA01.01', 'question_text' => 'Establish a monitoring approach', 'maturity_level' => 1, 'evidence_requirement' => 'Monitoring framework', 'guidance' => 'Define monitoring methodology'],
                ['code' => 'MEA01.02', 'question_text' => 'Set performance and conformance targets', 'maturity_level' => 2, 'evidence_requirement' => 'Performance targets', 'guidance' => 'Define performance metrics and targets'],
                ['code' => 'MEA01.03', 'question_text' => 'Collect and process performance and conformance data', 'maturity_level' => 2, 'evidence_requirement' => 'Data collection procedures', 'guidance' => 'Gather performance data'],
                ['code' => 'MEA01.04', 'question_text' => 'Analyze and report performance', 'maturity_level' => 3, 'evidence_requirement' => 'Performance reports', 'guidance' => 'Report on IT performance'],
                ['code' => 'MEA01.05', 'question_text' => 'Ensure implementation of corrective actions', 'maturity_level' => 4, 'evidence_requirement' => 'Corrective action records', 'guidance' => 'Follow up on improvement actions'],
            ],

            'MEA02' => [
                ['code' => 'MEA02.01', 'question_text' => 'Monitor internal controls', 'maturity_level' => 1, 'evidence_requirement' => 'Control monitoring logs', 'guidance' => 'Review control effectiveness'],
                ['code' => 'MEA02.02', 'question_text' => 'Review business process controls effectiveness', 'maturity_level' => 2, 'evidence_requirement' => 'Control assessment reports', 'guidance' => 'Evaluate process control adequacy'],
                ['code' => 'MEA02.03', 'question_text' => 'Perform control self-assessments', 'maturity_level' => 2, 'evidence_requirement' => 'Self-assessment results', 'guidance' => 'Conduct control self-assessments'],
                ['code' => 'MEA02.04', 'question_text' => 'Identify and report control deficiencies', 'maturity_level' => 3, 'evidence_requirement' => 'Control deficiency reports', 'guidance' => 'Document and report control gaps'],
                ['code' => 'MEA02.05', 'question_text' => 'Ensure implementation of corrective actions', 'maturity_level' => 4, 'evidence_requirement' => 'Remediation tracking', 'guidance' => 'Track control improvements'],
            ],

            'MEA03' => [
                ['code' => 'MEA03.01', 'question_text' => 'Identify external compliance requirements', 'maturity_level' => 1, 'evidence_requirement' => 'Compliance requirements register', 'guidance' => 'Document regulatory requirements'],
                ['code' => 'MEA03.02', 'question_text' => 'Optimize response to external requirements', 'maturity_level' => 2, 'evidence_requirement' => 'Compliance strategy', 'guidance' => 'Plan compliance approach'],
                ['code' => 'MEA03.03', 'question_text' => 'Confirm external compliance', 'maturity_level' => 3, 'evidence_requirement' => 'Compliance attestations', 'guidance' => 'Verify compliance with requirements'],
                ['code' => 'MEA03.04', 'question_text' => 'Obtain assurance of external compliance', 'maturity_level' => 4, 'evidence_requirement' => 'Audit reports', 'guidance' => 'Obtain independent compliance assurance'],
            ],
        ];
    }
}

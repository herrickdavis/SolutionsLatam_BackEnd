<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TelemetriaCriterioValidacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 1,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'CO_FLOW_Avg',
            'criterio' => '0.350<[CO_FLOW_Avg]<1.5',
            'aplicacion' => '0.350<[CO_FLOW_Avg]<1.5',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 2,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'CO_PRES_Avg',
            'criterio' => '250<[CO_PRES_Avg]<1000',
            'aplicacion' => '250<[CO_PRES_Avg]<1000',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 3,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'CO_AGC_INT_Avg',
            'criterio' => '150000<[CO_AGC_INT_Avg]<300000',
            'aplicacion' => '150000<[CO_AGC_INT_Avg]<300000',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 4,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'CO_CHAMBER_TEMP_Avg',
            'criterio' => '8<[CO_CHAMBER_TEMP_Avg]<47',
            'aplicacion' => '8<[CO_CHAMBER_TEMP_Avg]<47',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 5,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'CO_INTERNAL_TEMP_Avg',
            'criterio' => '40<[CO_INTERNAL_TEMP_Avg]<59',
            'aplicacion' => '40<[CO_INTERNAL_TEMP_Avg]<59',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 6,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'CO_MOTOR_SPEED_Avg',
            'criterio' => 'CO_MOTOR_SPEED_Avg > 99',
            'aplicacion' => '[CO_MOTOR_SPEED_Avg] > 99',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 7,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'SO2_FLOW_Avg',
            'criterio' => '0<SO2_FLOW_Avg<1',
            'aplicacion' => '0<[SO2_FLOW_Avg]<1',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 8,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'SO2_PRES_Avg',
            'criterio' => '400<[SO2_PRES_Avg]<1000',
            'aplicacion' => '400<[SO2_PRES_Avg]<1000',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 9,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'SO2_LAMP_INT_Avg',
            'criterio' => '40<[SO2_LAMP_INT_Avg]<100',
            'aplicacion' => '40<[SO2_LAMP_INT_Avg]<100',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 10,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'SO2_LAMP_VOLT_Avg',
            'criterio' => '750<[SO2_LAMP_VOLT_Avg]<1200',
            'aplicacion' => '750<[SO2_LAMP_VOLT_Avg]<1200',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 11,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'SO2_INTERNAL_TEMP_Avg',
            'criterio' => '8<[SO2_INTERNAL_TEMP_Avg]<47',
            'aplicacion' => '8<[SO2_INTERNAL_TEMP_Avg]<47',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 12,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'NO_FLOW_Avg',
            'criterio' => '0.750<[NO_FLOW_Avg]<1',
            'aplicacion' => '0.750<[NO_FLOW_Avg]<1',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 13,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'NO_PRES_Avg',
            'criterio' => '150<[NO_PRES_Avg]<300',
            'aplicacion' => '150<[NO_PRES_Avg]<300',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 14,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'NO_INTERNAL_TEMP_Avg',
            'criterio' => '15<[NO_INTERNAL_TEMP_Avg]<45',
            'aplicacion' => '15<[NO_INTERNAL_TEMP_Avg]<45',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 15,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'PM25_AMB_RH_Avg',
            'criterio' => '[PM25_AMB_RH_Avg]<95',
            'aplicacion' => '[PM25_AMB_RH_Avg]<95',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 16,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'PM25_AMB_TEMP_Avg',
            'criterio' => '-20<[PM25_AMB_TEMP_Avg]<60',
            'aplicacion' => '-20<[PM25_AMB_TEMP_Avg]<60',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 17,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'PM25_BARO_PRESS_Avg',
            'criterio' => '5<[PM25_BARO_PRESS_Avg]<50',
            'aplicacion' => '5<[PM25_BARO_PRESS_Avg]<50',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 18,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'PM25_FLOW_Avg',
            'criterio' => '[PM25_FLOW_Avg] == 1.2',
            'aplicacion' => '[PM25_FLOW_Avg] == 1.2',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 19,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'PM25_SAMPLE_RH_Avg',
            'criterio' => '[PM25_SAMPLE_RH_Avg]<95',
            'aplicacion' => '[PM25_SAMPLE_RH_Avg]<95',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 20,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'PM25_BARO_PRESS_Avg',
            'criterio' => '-50<[PM25_BARO_PRESS_Avg]<5',
            'aplicacion' => '-50<[PM25_BARO_PRESS_Avg]<5',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 21,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'PM25_FLOW_Avg',
            'criterio' => '[PM25_FLOW_Avg] == 1.2',
            'aplicacion' => '[PM25_FLOW_Avg] == 1.2',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 22,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'PM25_SAMPLE_RH_Avg',
            'criterio' => '[PM25_SAMPLE_RH_Avg]<95',
            'aplicacion' => '[PM25_SAMPLE_RH_Avg]<95',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 23,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'PM10_AMB_RH_Avg',
            'criterio' => '[PM10_AMB_RH_Avg]<95',
            'aplicacion' => '[PM10_AMB_RH_Avg]<95',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 24,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'PM10_AMB_TEMP_Avg',
            'criterio' => '-20<[PM10_AMB_TEMP_Avg]<60',
            'aplicacion' => '-20<[PM10_AMB_TEMP_Avg]<60',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 25,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'PM10_BARO_PRESS_Avg',
            'criterio' => '-50<[PM10_BARO_PRESS_Avg]<5',
            'aplicacion' => '-50<[PM10_BARO_PRESS_Avg]<5',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 26,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'PM10_FLOW_Avg',
            'criterio' => '[PM10_FLOW_Avg] == 1.2',
            'aplicacion' => '[PM10_FLOW_Avg] == 1.2',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 27,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'PM10_SAMPLE_RH_Avg',
            'criterio' => '[PM10_SAMPLE_RH_Avg]<95',
            'aplicacion' => '[PM10_SAMPLE_RH_Avg]<95',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 28,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'WS_ms_S_WVT',
            'criterio' => '[WS_ms_S_WVT]>=5',
            'aplicacion' => '[WS_ms_S_WVT]>=5',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 29,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '2',
            'descripcion' => 'Condiciones de Operatividad',
            'variables' => 'Rain_mm_Tot',
            'criterio' => '[Rain_mm_Tot]>=1',
            'aplicacion' => '[Rain_mm_Tot]>=1',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 30,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'CO_FLOW_Avg',
            'criterio' => '[CO_FLOW_Avg]>=0',
            'aplicacion' => '[CO_FLOW_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 31,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'CO_PRES_Avg',
            'criterio' => '[CO_PRES_Avg]>=0',
            'aplicacion' => '[CO_PRES_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 32,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'CO_PRES_Avg',
            'criterio' => '[CO_PRES_Avg]>=0',
            'aplicacion' => '[CO_PRES_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 33,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'CO_AGC_INT_Avg',
            'criterio' => '[CO_AGC_INT_Avg]>=0',
            'aplicacion' => '[CO_AGC_INT_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 34,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'CO_CHAMBER_TEMP_Avg',
            'criterio' => '[CO_CHAMBER_TEMP_Avg]>=0',
            'aplicacion' => '[CO_CHAMBER_TEMP_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 35,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'CO_INTERNAL_TEMP_Avg',
            'criterio' => '[CO_INTERNAL_TEMP_Avg]>=0',
            'aplicacion' => '[CO_INTERNAL_TEMP_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 36,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'CO_MOTOR_SPEED_Avg',
            'criterio' => '[CO_MOTOR_SPEED_Avg]>=0',
            'aplicacion' => '[CO_MOTOR_SPEED_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 37,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'CO_MOTOR_SPEED_Avg',
            'criterio' => '[CO_MOTOR_SPEED_Avg]>=0',
            'aplicacion' => '[CO_MOTOR_SPEED_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 38,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'SO2_FLOW_Avg',
            'criterio' => '[SO2_FLOW_Avg]>=0',
            'aplicacion' => '[SO2_FLOW_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 39,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'SO2_PRES_Avg',
            'criterio' => '[SO2_PRES_Avg]>=0',
            'aplicacion' => '[SO2_PRES_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 40,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'SO2_LAMP_INT_Avg',
            'criterio' => '[SO2_LAMP_INT_Avg]>=0',
            'aplicacion' => '[SO2_LAMP_INT_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 41,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'SO2_LAMP_VOLT_Avg',
            'criterio' => '[SO2_LAMP_VOLT_Avg]>=0',
            'aplicacion' => '[SO2_LAMP_VOLT_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 42,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'SO2_INTERNAL_TEMP_Avg',
            'criterio' => '[SO2_INTERNAL_TEMP_Avg]>=0',
            'aplicacion' => '[SO2_INTERNAL_TEMP_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 43,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'NO_FLOW_Avg',
            'criterio' => '[NO_FLOW_Avg]>=0',
            'aplicacion' => '[NO_FLOW_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 44,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'NO_PRES_Avg',
            'criterio' => '[NO_PRES_Avg]>=0',
            'aplicacion' => '[NO_PRES_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 45,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'NO_INTERNAL_TEMP_Avg',
            'criterio' => '[NO_INTERNAL_TEMP_Avg]>=0',
            'aplicacion' => '[NO_INTERNAL_TEMP_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 46,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'PM25_AMB_RH_Avg',
            'criterio' => '[PM25_AMB_RH_Avg]>=0',
            'aplicacion' => '[PM25_AMB_RH_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 47,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'PM25_BARO_PRESS_Avg',
            'criterio' => '[PM25_BARO_PRESS_Avg]>=0',
            'aplicacion' => '[PM25_BARO_PRESS_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 48,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'PM25_FLOW_Avg',
            'criterio' => '[PM25_FLOW_Avg]>=0',
            'aplicacion' => '[PM25_FLOW_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 49,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'PM25_FLOW_PRESS_Avg',
            'criterio' => '[PM25_FLOW_PRESS_Avg]>=0',
            'aplicacion' => '[PM25_FLOW_PRESS_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 50,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'PM25_SAMPLE_RH_Avg',
            'criterio' => '[PM25_SAMPLE_RH_Avg]>=0',
            'aplicacion' => '[PM25_SAMPLE_RH_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 51,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'PM10_AMB_RH_Avg',
            'criterio' => '[PM10_AMB_RH_Avg]>=0',
            'aplicacion' => '[PM10_AMB_RH_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 52,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'PM10_AMB_TEMP_Avg',
            'criterio' => '[PM10_AMB_TEMP_Avg]>=0',
            'aplicacion' => '[PM10_AMB_TEMP_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 53,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'PM10_BARO_PRESS_Avg',
            'criterio' => '[PM10_BARO_PRESS_Avg]>=0',
            'aplicacion' => '[PM10_BARO_PRESS_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 54,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'PM10_FLOW_Avg',
            'criterio' => '[PM10_FLOW_Avg]>=0',
            'aplicacion' => '[PM10_FLOW_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 55,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'PM10_SAMPLE_RH_Avg',
            'criterio' => '[PM10_SAMPLE_RH_Avg]>=0',
            'aplicacion' => '[PM10_SAMPLE_RH_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 56,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'PresionAtmosferica_Avg',
            'criterio' => '[PresionAtmosferica_Avg]>=0',
            'aplicacion' => '[PresionAtmosferica_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 57,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'HumedadRelativa_Avg',
            'criterio' => '[HumedadRelativa_Avg]>=0',
            'aplicacion' => '[HumedadRelativa_Avg]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 58,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'WS_ms_S_WVT',
            'criterio' => '[WS_ms_S_WVT]>=0',
            'aplicacion' => '[WS_ms_S_WVT]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 59,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'WindDir_D1_WVT',
            'criterio' => '[WindDir_D1_WVT]>=0',
            'aplicacion' => '[WindDir_D1_WVT]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 60,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'Rain_mm_Tot',
            'criterio' => '[Rain_mm_Tot]>=0',
            'aplicacion' => '[Rain_mm_Tot]>=0',
        ]);
        DB::table('telemetria_criterios_validacions')->insert([
            'id' => 62,
            'empresa_id' => '947',
            'tipo_criterio' => '1',
            'tipo_estado' => '3',
            'descripcion' => 'Descarte',
            'variables' => 'RadiacionSolar_Avg',
            'criterio' => '[RadiacionSolar_Avg]>=0',
            'aplicacion' => '[RadiacionSolar_Avg]>=0',
        ]);
    }
}

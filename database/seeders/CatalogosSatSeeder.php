<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FormaPago;
use App\Models\MetodoPago;
use App\Models\UsoCfdi;
use App\Models\RegimenFiscal;
use App\Models\ObjetoImpuesto;
use Illuminate\Support\Facades\DB;

class CatalogosSatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('formas_pago')->insert([
            ['clave' => '01', 'descripcion' => 'Efectivo'],
            ['clave' => '02', 'descripcion' => 'Cheque nominativo'],
            ['clave' => '03', 'descripcion' => 'Transferencia electrónica de fondos'],
            ['clave' => '04', 'descripcion' => 'Tarjeta de crédito'],
            ['clave' => '05', 'descripcion' => 'Monedero electrónico'],
            ['clave' => '06', 'descripcion' => 'Dinero electrónico'],
            ['clave' => '08', 'descripcion' => 'Vales de despensa'],
            ['clave' => '12', 'descripcion' => 'Dación en pago'],
            ['clave' => '13', 'descripcion' => 'Pago por subrogación'],
            ['clave' => '14', 'descripcion' => 'Pago por consignación'],
            ['clave' => '15', 'descripcion' => 'Condonación'],
            ['clave' => '17', 'descripcion' => 'Compensación'],
            ['clave' => '23', 'descripcion' => 'Novación'],
            ['clave' => '24', 'descripcion' => 'Confusión'],
            ['clave' => '25', 'descripcion' => 'Remisión de deuda'],
            ['clave' => '26', 'descripcion' => 'Prescripción o caducidad'],
            ['clave' => '27', 'descripcion' => 'A satisfacción del acreedor'],
            ['clave' => '28', 'descripcion' => 'Tarjeta de débito'],
            ['clave' => '29', 'descripcion' => 'Tarjeta de servicios'],
            ['clave' => '30', 'descripcion' => 'Aplicación de anticipos'],
            ['clave' => '31', 'descripcion' => 'Intermediario pagos'],
            ['clave' => '99', 'descripcion' => 'Por definir'],
        ]);

        DB::table('metodos_pago')->insert([

            ['clave' => 'PUE', 'descripcion' => 'Pago en una sola exhibición'],
            ['clave' => 'PPD', 'descripcion' => 'Pago en parcialidades o diferido'],
        ]);

        DB::table('usos_cfdi')->insert([
            ['clave' => 'G01', 'descripcion' => 'Adquisición de mercancías'],
            ['clave' => 'G02', 'descripcion' => 'Devoluciones, descuentos o bonificaciones'],
            ['clave' => 'G03', 'descripcion' => 'Gastos en general'],
            ['clave' => 'I01', 'descripcion' => 'Construcciones'],
            ['clave' => 'I02', 'descripcion' => 'Mobilario y equipo de oficina por inversiones'],
            ['clave' => 'I03', 'descripcion' => 'Equipo de transporte'],
            ['clave' => 'I04', 'descripcion' => 'Equipo de cómputo y accesorios'],
            ['clave' => 'I05', 'descripcion' => 'Dados, troqueles, moldes, matrices y herramental'],
            ['clave' => 'I06', 'descripcion' => 'Comunicaciones telefónicas'],
            ['clave' => 'I07', 'descripcion' => 'Comunicaciones satelitales'],
            ['clave' => 'I08', 'descripcion' => 'Otra maquinaria y equipo'],
            ['clave' => 'D01', 'descripcion' => 'Honorarios médicos, dentales y gastos hospitalarios.'],
            ['clave' => 'D02', 'descripcion' => 'Gastos médicos por incapacidad o discapacidad'],
            ['clave' => 'D03', 'descripcion' => 'Gastos funerales'],
            ['clave' => 'D04', 'descripcion' => 'Donativos'],
            ['clave' => 'D05', 'descripcion' => 'Intereses reales efectivamente pagados por créditos hipotecarios'],
            ['clave' => 'D06', 'descripcion' => 'Aportaciones voluntarias al SAR'],
            ['clave' => 'D07', 'descripcion' => 'Primas por seguros de gastos médicos'],
            ['clave' => 'D08', 'descripcion' => 'Gastos de transportación escolar obligatoria'],
            ['clave' => 'D09', 'descripcion' => 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones'],
            ['clave' => 'D10', 'descripcion' => 'Pagos por servicios educativos (colegiaturas)'],
            ['clave' => 'P01', 'descripcion' => 'Por definir'],
        ]);

        DB::table('regimenes_fiscales')->insert([
            ['clave' => '601', 'descripcion' => 'General de Ley Personas Morales'],
            ['clave' => '603', 'descripcion' => 'Personas Morales con Fines no Lucrativos'],
            ['clave' => '605', 'descripcion' => 'Sueldos y Salarios e Ingresos Asimilados a Salarios'],
            ['clave' => '606', 'descripcion' => 'Arrendamiento'],
            ['clave' => '607', 'descripcion' => 'Régimen de Enajenación o Adquisición de Bienes'],
            ['clave' => '608', 'descripcion' => 'Demás ingresos'],
            ['clave' => '610', 'descripcion' => 'Residentes en el Extranjero sin Establecimiento Permanente en México'],
            ['clave' => '611', 'descripcion' => 'Ingresos por Dividendos (socios y accionistas)'],
            ['clave' => '612', 'descripcion' => 'Personas Físicas con Actividades Empresariales y Profesionales'],
            ['clave' => '614', 'descripcion' => 'Ingresos por intereses'],
            ['clave' => '615', 'descripcion' => 'Régimen de los ingresos por obtención de premios'],
            ['clave' => '616', 'descripcion' => 'Sin obligaciones fiscales'],
            ['clave' => '620', 'descripcion' => 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos'],
            ['clave' => '621', 'descripcion' => 'Incorporación Fiscal'],
            ['clave' => '622', 'descripcion' => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras'],
            ['clave' => '623', 'descripcion' => 'Opcional para Grupos de Sociedades'],
            ['clave' => '624', 'descripcion' => 'Coordinados'],
            ['clave' => '625', 'descripcion' => 'Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas'],
            ['clave' => '626', 'descripcion' => 'Régimen Simplificado de Confianza'],
        ]);

        DB::table('objetos_impuesto')->insert([
            ['clave' => '01', 'descripcion' => 'No objeto de impuesto'],
            ['clave' => '02', 'descripcion' => 'Sí objeto de impuesto'],
            ['clave' => '03', 'descripcion' => 'Sí objeto de impuesto y no obligado al desglose'],
        ]);
    }
}

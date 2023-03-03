export interface PeriodUnit {
  unitCode: string;
  value: number | null;
}

export interface Period {
  detailedForecast: string;
  dewpoint: PeriodUnit;
  endTime: string;
  icon: string;
  isDaytime: boolean;
  name: string;
  number: number; // Unique Identificator, like ID
  probabilityOfPrecipitation: PeriodUnit;
  relativeHumidity: PeriodUnit;
  shortForecast: string;
  startTime: string;
  temperature: number | null;
  temperatureTrend: number | null;
  temperatureUnit: string;
  windDirection: string;
  windSpeed: string;
}

export interface Forecast {
  status: boolean;
  error?: string;
  periods?: Period[];
}
